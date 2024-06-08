<?php 

namespace METRIC\App\Service;

class Utils
{    
    /**
     * getDocumentType
     * Obtiene el tipo de documento con el que se va a identificar en el 
     * proceso de video identificación (card ID / Passport)
     *
     * @return string
     */
    public static function getDocumentType(): string
    {
        $document_type = "XX_Passport_YYYY";
        return $document_type;
    }
}