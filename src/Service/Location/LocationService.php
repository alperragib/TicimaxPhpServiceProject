<?php

namespace AlperRagib\Ticimax\Service\Location;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxHelpers;
use AlperRagib\Ticimax\TicimaxRequest;

class LocationService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/CustomServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }
    /**
     * Get list of countries
     * 
     * @param int|null $countryId Filter by country ID
     * @param string|null $countryCode Filter by country code
     * @return ApiResponse
     */
    public function getCountries(?int $countryId = null, ?string $countryCode = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        $request = [
            'FiltreUlkeID' => $countryId ?? -1,
            'FiltreUlkeKodu' => $countryCode ?? ''
        ];

        try {
            $response = $client->__soapCall("SelectUlkeler", [
                [
                    'UyeKodu' => $this->request->key,
                    'SelectRequest' => (object)$request,
                ]
            ]);

            if (!$response || !isset($response->SelectUlkelerResult)) {
                return new ApiResponse(false, 'No countries found', []);
            }

            $countries = [];
            $countriesArr = $response->SelectUlkelerResult->KargoUlke ?? [];
            if (is_object($countriesArr)) {
                $countriesArr = [$countriesArr];
            }
            
            foreach ($countriesArr as $country) {
                $countries[] = new BaseModel($country);
            }

            return new ApiResponse(true, 'Countries retrieved successfully', $countries);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }

    /**
     * Get list of cities
     * 
     * @param int|null $cityId Filter by city ID
     * @param int|null $countryId Filter by country ID
     * @return ApiResponse
     */
    public function getCities(?int $cityId = null, ?int $countryId = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        $request = [
            'FiltreIlID' => $cityId ?? -1,
            'FiltreUlkeID' => $countryId ?? -1
        ];

        try {
            $response = $client->__soapCall("SelectIller", [
                [
                    'UyeKodu' => $this->request->key,
                    'SelectRequest' => (object)$request,
                ]
            ]);
            
            if (!$response || !isset($response->SelectIllerResult)) {
                return new ApiResponse(false, 'No cities found', []);
            }

            $cities = [];
            $citiesArr = $response->SelectIllerResult->KargoIl ?? [];
            if (is_object($citiesArr)) {
                $citiesArr = [$citiesArr];
            }
            
            foreach ($citiesArr as $city) {
                $cities[] = new BaseModel(TicimaxHelpers::objectToArray($city));
            }

            return new ApiResponse(true, 'Cities retrieved successfully', $cities);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }

    /**
     * Get list of districts
     * 
     * @param int|null $districtId Filter by district ID
     * @param int|null $cityId Filter by city ID
     * @return ApiResponse
     */
    public function getDistricts(?int $districtId = null, ?int $cityId = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        $request = [
            'FiltreIlceID' => $districtId ?? -1,
            'FiltreIlID' => $cityId ?? -1
        ];

        try {
            $response = $client->__soapCall("SelectIlceler", [
                [
                    'UyeKodu' => $this->request->key,
                    'SelectRequest' => (object)$request,
                ]
            ]);
            
            if (!$response || !isset($response->SelectIlcelerResult)) {
                return new ApiResponse(false, 'No districts found', []);
            }

            $districts = [];
            $districtsArr = $response->SelectIlcelerResult->KargoIlce ?? [];
            if (is_object($districtsArr)) {
                $districtsArr = [$districtsArr];
            }
            
            foreach ($districtsArr as $district) {
                $districts[] = new BaseModel($district);
            }

            return new ApiResponse(true, 'Districts retrieved successfully', $districts);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }
    
} 