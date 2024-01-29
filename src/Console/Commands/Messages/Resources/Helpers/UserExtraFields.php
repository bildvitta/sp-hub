<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

trait UserExtraFields
{
    /**
     * @param array $fillable
     * @return bool
     */
    protected function userHasExtraFields(array $fillable): bool
    {
        $extraFields = [
            'document', 
            'address', 
            'street_number', 
            'complement',
            'city',
            'state',
            'postal_code',
        ];
        foreach($extraFields as $extraField) {
            if (!in_array($extraField, $fillable)) {
                return false;
            }
        }

        return true;
    }
}
