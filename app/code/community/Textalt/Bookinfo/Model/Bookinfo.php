<?php

require_once('app/code/local/Textalt/Bookinfo/lib/Curlclient.php');

class Textalt_Bookinfo_Model_Bookinfo extends Mage_Core_Model_Abstract 
{

    const bookimagepath = 'bookimages/';

    protected function _construct()
    {
        $this->_init('bookinfo/bookinfo');

    }   

    public function loadByIsbn($isbn)
    {

        $collection = $this->getCollection()->addFilter('isbn', $isbn );

        if ( $collection->count() > 0 ) {
            // If the record, exists, load it
            $this->load( $collection->getFirstItem()->getId() );
        } else {        
            // otherwise, try to get it from google
            $this->getNewBook( $isbn );
        }

        return $this;
    }

    // Books API key
    // AIzaSyDOiDWRwJQwNoWIdtgwlW4RXIH73NpeEPM
    const googleUrl = 'https://www.googleapis.com/books/v1/volumes?key=AIzaSyDOiDWRwJQwNoWIdtgwlW4RXIH73NpeEPM&q=isbn:';

    private function getNewBook($isbn) 
    {
        $curlClient = new Textalt_Bookinfo_lib_Curlclient();

        $url = self::googleUrl.$isbn;

        // Mage::log( $url );

        $json = $curlClient->loadHtml( $url );

        try {
            // this is to make sure it's legit json 
            $array = Zend_Json::decode( $json );
            
            $this->setIsbn($isbn);
            $this->setJson($json);
            $this->save();
            return true;
        } catch ( Exception $ex ) {
            Mage::log( __CLASS__.": failed to get book data for isbn ".$isbn );
        }

        return false;
    }



    public function getSmallThumbnail($isSecure = false)
    {
        return $this->getGoogleImage($isSecure,'smallThumbnail');
    }
    public function getImage_smallThumbnail() 
    {
        return $this->getSmallthumbnailpath();
    }
    public function setImage_smallThumbnail( $data )
    {
        $this->setSmallthumbnailpath( $data );
    }





    public function getThumbnail($isSecure = false)
    {
        return $this->getGoogleImage($isSecure,'thumbnail');
    }
    public function getImage_Thumbnail() 
    {
        return $this->getThumbnailpath();
    }
    public function setImage_Thumbnail( $data )
    {
        $this->setThumbnailpath( $data );
    }





    protected function getGoogleImage($isSecure = false,$type)
    {
        if ( ! $this->getId() ) {
            // no book loaded, nothing to find
            return;
        }
    
        $fcnName = "getImage_".$type;
        $theImagePath = $this->$fcnName();

        if ( isset( $theImagePath ) && ( $theImagePath != "placeholder" ) ) {
            // if we've already got the image, send the url
            return Mage::getUrl('media/'.self::bookimagepath, array( "_secure" => $isSecure ) ). $theImagePath;
        } else {
            // otherwise, get the image from google

            $bookDataArray = Zend_Json::decode( $this->getJson() );

            // Mage::log( $bookDataArray );

            if ( isset( $bookDataArray["items"][0]["volumeInfo"]["imageLinks"][$type] ) ) {

                try {

                    $googleUrl = $bookDataArray["items"][0]["volumeInfo"]["imageLinks"][$type];

                    $curlClient = new Textalt_Bookinfo_lib_Curlclient();                        

                    $image = $curlClient->loadHtml( $googleUrl );

                    if ( strlen( $image ) > 0 ) {

                        $filePath = "media".DS.self::bookimagepath;

                        $fullPathAndName = tempnam( $filePath,$type."_" );
                        unlink( $fullPathAndName );
        
                        $fullPathAndName .= ".jpg";

                        $fileName = pathinfo( $fullPathAndName, PATHINFO_BASENAME );


                        $fp = fopen( $fullPathAndName,'w');
                        fwrite( $fp, $image );
                        fclose( $fp );

                        $fcnName = "setImage_".$type;
                        $this->$fcnName( $fileName );
                        $this->save();

                        return Mage::getUrl('/', array( "_secure" => $isSecure ) ).'media/'.self::bookimagepath.$fileName;
                    } else {
                        Mage::log( 'bookinfo: failed to get the image ');
                        $fcnName = "getPlaceholder_".$type;
                        return $this->$fcnName( $isSecure );
                    }
                } catch ( Exception $ex ) {
                    Mage::logException( $ex );
                    Mage::log("bookinfo: exception occurred getting image: ".$ex->getMessage() );
                    $fcnName = "getPlaceholder_".$type;
                    return $this->$fcnName( $isSecure );
                }

            } else {
                Mage::log( 'bookinfo:  json seems to be missing image');
                $fcnName = "setImage_".$type;
                $this->$fcnName( "placeholder" );
                $this->save();

                $fcnName = "getPlaceholder_".$type;
                return $this->$fcnName( $isSecure );
            }

        }

    }

    
    protected $smallPlaceholder;
    private function getPlaceholder_smallThumbnail($isSecure = false)
    {
        if ( !isset( $this->smallPlaceholder ) ) {
            $smallThumbnailPlaceholder = Mage::getStoreConfig("catalog/placeholder/small_image_placeholder");
            $fullPath =  Mage::getUrl('/', array( "_secure" => $isSecure ) ).'media/catalog/product/placeholder/'.$smallThumbnailPlaceholder;
            $this->smallPlaceholder = $fullPath;
        }
        return $this->smallPlaceholder;
    }


    protected $thumbnail;
    public function getPlaceholder_thumbnail() 
    {
        if ( !isset( $this->thumbnail ) ) {
            $thumbnailPlaceholder = Mage::getStoreConfig("catalog/placeholder/thumbnail_placeholder");
            $fullPath =  Mage::getUrl('/', array( "_secure" => $isSecure ) ).'media/catalog/product/placeholder/'.$thumbnailPlaceholder;
            $this->thumbnail = $fullPath;
        }
        return $this->thumbnail;
    }

    


    public function getBookData()
    {
        $json = $this->getJson();
        
        if ( isset( $json ) ) {
            return Zend_Json::decode( $json );
        } else {
            return array();
        }
    }    


} 


?>
