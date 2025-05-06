<?php

namespace App\Helpers\Errors;

class ErrorHandler {
    public static function formatErrors($errors)
    {
          $errorHtml = '<div class="alert m-0 p-1" role="alert">';
          $errorHtml .= '<ul class="list-group list-group-flush m-0 p-0">'; // Bootstrap list group
     
          foreach ($errors->all() as $error) {
               $errorHtml .= '<li class="list-group-item text-sm text-center py-0">' . $error . '</li>'; // Added text-center
          }
          
          $errorHtml .= '</ul>';
          $errorHtml .= '</div>';
          
          return $errorHtml;
    }
    
    
    public static function MethodFallbackErrors()
    {
          $errorHtml = '<small>An error has occurred. Please contact the Sinag IT department for support.</small>';
          return $errorHtml;
    }
}



