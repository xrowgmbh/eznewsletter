<?php

class ezpMailComposer extends ezcMailComposer
{
	/**
     * Holds the unique ID's.
     *
     * @var int
     */
    private static $idCounter = 0;

    /**
     * Constructs an empty ezcMailComposer object.
     *
     * @param ezcMailComposerOptions $options
     */
    public function __construct( ezcMailComposerOptions $options = null )
    {
        parent::__construct( $options );
        $this->properties['charset'] = 'utf-8';
        $this->properties['subjectCharset'] = 'utf-8';
    }
    public function generateMessageId( $messagekey = null )
    {
    	$idhost = $this->from != null && $this->from->email != '' ? $this->from->email : 'localhost';
		if ( strpos( $hostname, '@' ) !== false )
        {
            $msgidhost = strstr( $hostname, '@' );
        }
        else
        {
            $msgidhost = '@' . $hostname;
        }
        if ( $messagekey )
        {
        	$messagekey .= '.';
        }
        else 
        { 
        	$messagekey = '';
        
        }
        $this->messageId = '<' . $messagekey . date( 'YmdGHjs' ) . '.' . getmypid() . '.' . self::$idCounter++ . $msgidhost . '>';
    }
    /**
     * Personalize email based on import parameters
     */
    public function personalize( $userData, $enabled = true )
    {
        $matchArray = array();

        foreach( $userData as $key => $value )
        {
            if ( ( $enabled === false ) and ( $key === 'name') ) {
                $matchArray['[' . $key . ']'] = '';
            }
            else
            {
                $matchArray['[' . $key . ']'] = $value;
            }
        }
		$this->plainText = str_replace( array_keys( $matchArray ), array_values( $matchArray ), $this->plainText );
		$this->htmlText = str_replace( array_keys( $matchArray ), array_values( $matchArray ), $this->htmlText );
    }
    /**
     * Returns an ezcMailPart based on the HTML provided.
     *
     * This method adds local files/images to the mail itself using a
     * {@link ezcMailMultipartRelated} object.
     *
     * @throws ezcBaseFileNotFoundException
     *         if $fileName does not exists.
     * @throws ezcBaseFilePermissionProblem
     *         if $fileName could not be read.
     * @return ezcMailPart
     */
    private function generateHtmlPart()
    {
        $result = false;
        if ( $this->htmlText != '' )
        {
            $matches = array();
            if ( $this->options->automaticImageInclude === true )
            {
                // recognize file:// and file:///, pick out the image, add it as a part and then..:)
                preg_match_all( '(
                    <img \\s+[^>]*
                        src\\s*=\\s*
                            (?:
                                (?# Match quoted attribute)
                                ([\'"])file://(?P<quoted>[^>]+)\\1

                                (?# Match unquoted attribute, which may not contain spaces)
                            |   file://(?P<unquoted>[^>\\s]+)
                        )
                    [^>]* >)ixU', $this->htmlText, $matches );
                // pictures/files can be added multiple times. We only need them once.
                $matches = array_filter( array_unique( array_merge( $matches['quoted'], $matches['unquoted'] ) ) );
            }

            $result = new ezcMailText( $this->htmlText, $this->charset, $this->encoding );
            $result->subType = "html";
            if ( count( $matches ) > 0 )
            {
                $htmlPart = $result;
                // wrap already existing message in an alternative part
                $result = new ezcMailMultipartRelated( $result );

                // create a filepart and add it to the related part
                // also store the ID for each part since we need those
                // when we replace the originals in the HTML message.
                foreach ( $matches as $fileName )
                {
                    if ( is_readable( $fileName ) )
                    {
                        // @todo waiting for fix of the fileinfo extension
                        // $contents = file_get_contents( $fileName );
                        $mimeType = null;
                        $contentType = null;
                        if ( ezcBaseFeatures::hasExtensionSupport( 'fileinfo' ) )
                        {
                            // if fileinfo extension is available
                            $filePart = new ezcMailFile( $fileName );
                        }
                        elseif ( ezcMailTools::guessContentType( $fileName, $contentType, $mimeType ) )
                        {
                            // if fileinfo extension is not available try to get content/mime type
                            // from the file extension
                            $filePart = new ezcMailFile( $fileName, $contentType, $mimeType );
                        }
                        else
                        {
                            // fallback in case fileinfo is not available and could not get content/mime
                            // type from file extension
                            $filePart = new ezcMailFile( $fileName, "application", "octet-stream" );
                        }
                        $cid = $result->addRelatedPart( $filePart );
                        // replace the original file reference with a reference to the cid
                        $this->htmlText = str_replace( 'file://' . $fileName, 'cid:' . $cid, $this->htmlText );
                    }
                    else
                    {
                        if ( file_exists( $fileName ) )
                        {
                            throw new ezcBaseFilePermissionException( $fileName, ezcBaseFileException::READ );
                        }
                        else
                        {
                            throw new ezcBaseFileNotFoundException( $fileName );
                        }
                        // throw
                    }
                }
                // update mail, with replaced URLs
                $htmlPart->text = $this->htmlText;
            }
        }
        return $result;
    }
}
?>
