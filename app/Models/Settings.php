<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {
    protected $table ="settings";
    protected $fileable_key = "settings";
    protected $fillable     = ['reportingOrganizationInfo',
        'publishingType',
        'registryInfo',
        'defaultFieldValues',
        'defaultFieldGroups'
    ];

    public function buildOrganizationReportingOrganizationInfo()
    {
        return json_decode($this->reportingOrganizationInfo, true);
    }

    public function buildOrganizationPublishingType()
    {
        return json_decode($this->publishingType, true);
    }

    public function buildOrganizationRegistryInfo()
    {
        return json_decode($this->registryInfo, true);
    }

    public function buildOrganizationDefaultFieldValues()
    {
        return json_decode($this->defaultFieldValues, true);
    }

    public function buildOrganizationDefaultFieldGroups()
    {
        return json_decode($this->defaultFieldGroups, true);
    }
}