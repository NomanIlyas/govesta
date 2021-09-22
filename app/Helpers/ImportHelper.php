<?php

namespace App\Helpers;

use App\Model\Geo\Country;
use App\Model\Geo\City;
use App\Model\Geo\District;
use App\Model\Property\Property;
use App\Model\Property\Image;
use App\Model\Property\Type;
use App\Model\Property\SubType;
use App\Model\Geo\Address;
use App\Model\General\Currency;
use App\Model\User\Agency;
use App\Model\Property\Analytics;
use App\Helpers\FileHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Enums\MarketStatus;
use App\Enums\Status;
use App\Model\Property\FloorPlan;

class ImportHelper
{

  public static function parseXML($folderName)
  {
    $directory = public_path('onoffice/' . $folderName . '/');

    $files = glob($directory . '/*xml');

    if (is_array($files)) {

      foreach ($files as $filename) {

        $loader = require(__DIR__ . '/../../vendor/autoload.php');
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $xmlString = file_get_contents($filename);
        $openImmo = $serializer->deserialize($xmlString, \Ujamii\OpenImmo\API\Openimmo::class, 'xml');

        foreach ($openImmo->getAnbieter() as $anbieter) {
          $agencyId = $anbieter->getAnbieternr();
          $agency = Agency::find($agencyId);
          if (isset($agency)) {
            foreach ($anbieter->getImmobilie() as $immobilie) {
              $techInfo = $immobilie->getVerwaltungTechn();
              $deleted = FALSE;
              $aktion = $techInfo->getAktion();
              if (isset($aktion)) {
                if ($aktion->getAktionart() === 'DELETE') {
                  $deleted = TRUE;
                }
              }
              if (isset($techInfo)) {
                $sprache = $techInfo->getSprache();
                //$syncId = $techInfo->getOpenimmoObid();
                $externalId = $techInfo->getObjektnrExtern();
                if (isset($externalId)) {
                  $syncId = $agencyId . '_' . $externalId;
                  $currentProperty = Property::with(['address'])->where('sync_id', $syncId)->first();
                  $property = isset($currentProperty) ? $currentProperty : new Property();
                  $address = isset($currentProperty) ? (isset($currentProperty->address_id) ? Address::find($currentProperty->address_id) : new Address())  : new Address();

                  $property->agency_id = $agencyId;
                  $property->sync_id = $syncId;

                  /** FREE TEXT */
                  $freeText = $immobilie->getFreitexte();
                  if (isset($freeText)) {
                    $lang = 'en';
                    if ($sprache == 'en-GB') {
                      $lang = 'en';
                    } else if ($sprache == 'de-DE') {
                      $lang = 'de';
                    } else if ($sprache == 'fr-FR') {
                      $lang = 'fr';
                    }
                    $property->translateOrNew($lang)->title = $freeText->getObjekttitel();
                    $property->translateOrNew($lang)->slug = str_slug($freeText->getObjekttitel()) . '-' . now()->timestamp;
                    $property->translateOrNew($lang)->description = $freeText->getObjektbeschreibung();
                  }

                  /** MARKET STATUS */
                  $property->market_status = self::getMarketStatus($immobilie);

                  /** GEO */
                  $geo = $immobilie->getGeo();
                  if (isset($geo)) {
                    $land = $geo->getLand();
                    if (isset($land)) {
                      $countryCode = $land->getIsoLand() ?? 'DEU';
                      $country = Country::where('code3', $countryCode)->first();
                      if (isset($country)) {
                        $address->country_id = $country->id;
                        $seperator = NULL;
                        $ort = $geo->getOrt();
                        if (stripos($ort, '/') !== false) {
                          $seperator = '/';
                        }
                        if (stripos($ort, ' / ') !== false) {
                          $seperator = ' / ';
                        }
                        if (stripos($ort, '-') !== false) {
                          $seperator = '-';
                        }
                        if (stripos($ort, ' - ') !== false) {
                          $seperator = ' - ';
                        }
                        if (stripos($ort, ',') !== false) {
                          $seperator = ', ';
                        }
                        if (isset($seperator)) {
                          $loc = explode($seperator, $ort);
                          $cityName = $loc[0];
                          $districtName = $loc[1] ?? NULL;
                        } else {
                          $cityName = $ort;
                          $districtName = NULL;
                        }
                        $city = City::whereTranslation('name', $cityName)->where('country_id', $address->country_id)->first();
                        $district = District::where('name', $districtName)->where('city_id', (isset($city) ? $city->id : NULL))->first();
                        if (isset($district)) {
                          $address->state_id = $district->state_id;
                          $address->city_id = $district->city_id;
                          $address->district_id = $district->id;
                        } else if (isset($city)) {
                          $address->state_id = $city->state_id;
                          $address->city_id = $city->id;
                        }
                        $address->postal_code = $geo->getPlz();
                        $address->street = $geo->getStrasse();
                        $address->street_number = $geo->getHausnummer();
                        $coordinates = $geo->getGeokoordinaten();
                        if (isset($district) && isset($coordinates)) {
                          $address->latitude = $coordinates->getBreitengrad();
                          $address->longitude = $coordinates->getLaengengrad();
                        }
                        $address->save();
                        $property->address_id = $address->id;
                      }
                    }
                  }

                  /** CATEGORY */
                  $category = $immobilie->getObjektkategorie();
                  if (isset($category)) {
                    $transaction = $category->getVermarktungsart();
                    if (isset($transaction)) {
                      if ($transaction->getKauf()) {
                        $property->transaction_type = 'buy';
                      } else if ($transaction->getMietePacht()) {
                        $property->transaction_type = 'rent';
                      } else if ($transaction->getLeasing()) {
                        $property->transaction_type = 'lease';
                      }
                    }
                    $objectType = $category->getObjektart();
                    if (isset($objectType)) {
                      $type = self::getType($objectType);
                      if (isset($type['type_id'])) {
                        $property->type_id = $type['type_id'];
                      }
                      if (isset($type['sub_type_id'])) {
                        $property->sub_type_id = $type['sub_type_id'];
                      }
                    }
                  }

                  /** ROOMS */
                  $roomsObj = $immobilie->getFlaechen();
                  if (isset($roomsObj)) {
                    $property->rooms = $roomsObj->getAnzahlZimmer();
                    $property->bedrooms = $roomsObj->getAnzahlSchlafzimmer();
                    $property->bathrooms = $roomsObj->getAnzahlBadezimmer();
                    $property->sqm = $roomsObj->getWohnflaeche();
                  }

                  /** PRICE */
                  $price = $immobilie->getPreise();
                  if (isset($price)) {
                    if ($property->transaction_type == 'rent') {
                      $property->price = $price->getNettokaltmiete();
                    } else {
                      $kaufPrice = $price->getKaufpreis();
                      if (isset($kaufPrice)) {
                        $property->price = $kaufPrice->getValue();
                      }
                    }
                    $currencyObj = $price->getWaehrung();
                    if (isset($currencyObj)) {
                      $currencyCode = $currencyObj->getIsoWaehrung();
                      $currency = Currency::where('code', strtolower($currencyCode))->first();
                      $property->currency_id = isset($currency) ? $currency->id : NULL;
                    }
                  } else {
                    $property->price_on_request = TRUE;
                  }

                  if ($deleted) {
                    $property->status = Status::Deleted;
                  } else {
                    if ($property->status === Status::Deleted) {
                      $property->status = Status::Draft;
                    }
                  }

                  // Saving...
                  $property->save();

                  /** IMAGES */
                  $imagesObj = $immobilie->getAnhaenge();
                  if (!$deleted && isset($imagesObj)) {
                    $images = $imagesObj->getAnhang() ?? [];
                    foreach ($images as $image) {
                      $group = $image->getGruppe();
                      if ($group === "TITELBILD" || $group === "BILD" || $group === "GRUNDRISS") {
                        $fileName = $image->getDaten()->getPfad();
                        $filePath = $directory . $fileName;
                        if (file_exists($filePath)) {
                          $finfo = new \finfo(FILEINFO_MIME_TYPE);
                          $castFile = new UploadedFile(
                            $filePath,
                            $fileName,
                            $finfo->file($filePath),
                            \filesize($filePath),
                            0,
                            false
                          );
                          $uploadedFile = NULL;
                          if ($group === "GRUNDRISS") {
                            if (self::checkFloor($property->id, $fileName)) {
                              $uploadedFile = FileHelper::upload("property-floor-plan", $castFile);
                            }
                          }
                          if ($group !== "GRUNDRISS") {
                            if (self::checkImage($property->id, $fileName)) {
                              $uploadedFile = FileHelper::upload("property", $castFile);
                            }
                          }
                          if (isset($uploadedFile) && isset($uploadedFile->id)) {
                            $file = $group === "GRUNDRISS" ? new FloorPlan() : new Image();
                            $file->property_id = $property->id;
                            $file->file_id = $uploadedFile->id;
                            $file->save();
                          }
                        }
                      }
                    }
                  }

                  if (isset($property->id)) {
                    $analytics = Analytics::where('property_id', $property->id)->first();
                    if (!isset($analytics)) {
                      Analytics::create(['property_id' => $property->id]);
                    }
                  }

                  \Log::channel('sentry')->info('OnOffice Import', [
                    'agency' => $agency->name,
                    'agency_id' => $agency->id,
                    'file_name' => $folderName,
                    'sync_id' => $property->sync_id
                  ]);
                }
              }
            }
          }
        }
      }
    }
  }

  public static function checkImage($propertyId, $fileName)
  {
    $exist = false;
    $images = Image::with(['file'])->where('property_id', $propertyId)->get();
    foreach ($images as $image) {
      if ($image->file && $image->file->original_name === $fileName) {
        $exist = true;
      }
    }
    return !$exist;
  }

  public static function checkFloor($propertyId, $fileName)
  {
    $exist = false;
    $images = FloorPlan::with(['file'])->where('property_id', $propertyId)->get();
    foreach ($images as $image) {
      if ($image->file && $image->file->original_name === $fileName) {
        $exist = true;
      }
    }
    return !$exist;
  }

  public static function delete_files($target)
  {
    if (!is_link($target) && is_dir($target)) {
      $files = array_diff(scandir($target), array('.', '..'));
      foreach ($files as $file) {
        self::delete_files("$target/$file");
      }
      rmdir($target);
    } elseif (is_file($target)) {
      unlink($target);
    }
  }

  public static function getMarketStatus($immobilie)
  {
    $status = MarketStatus::Open;
    $container = $immobilie->getZustandAngaben();
    if (isset($container)) {
      $item = $container->getVerkaufstatus();
      if (isset($item)) {
        $s = $item->getStand();
        if ($s == 'RESERVIERT') {
          $status = MarketStatus::Reserved;
        } else if ($s == 'VERKAUFT') {
          $status = MarketStatus::Sold;
        }
      }
    }
    return $status;
  }

  public static function getType($type)
  {
    $typeName = '';
    $subTypeName = '';
    if (count($type->getWohnung()) !== 0) {
      $typeName = 'wohnung';
      $subTypeName = $type->getWohnung()[0]->getWohnungtyp();
    } else if (count($type->getBueroPraxen()) !== 0) {
      $typeName = 'buropraxen';
      $subTypeName = $type->getBueroPraxen()[0]->getBueroTyp();
    } else if (count($type->getZimmer()) !== 0) {
      $typeName = 'zimmer';
      $subTypeName = $type->getZimmer()[0]->getZimmertyp();
    } else if (count($type->getHaus()) !== 0) {
      $typeName = 'haus';
      $subTypeName = $type->getHaus()[0]->getHaustyp();
    } else if (count($type->getSonstige()) !== 0) {
      $typeName = 'sonstige';
      $subTypeName = $type->getSonstige()[0]->getSonstigetyp();
    } else if (count($type->getGrundstueck()) !== 0) {
      $typeName = 'grundstuck';
      $subTypeName = $type->getGrundstueck()[0]->getGrundstTyp();
    } else if (count($type->getGastgewerbe()) !== 0) {
      $typeName = 'gastgewerbe';
      $subTypeName = $type->getGastgewerbe()[0]->getGastgewTyp();
    } else if (count($type->getHallenLagerProd()) !== 0) {
      $typeName = 'hallenlagerproduktion';
      $subTypeName = $type->getHallenLagerProd()[0]->getHallenTyp();
    } else if (count($type->getLandUndForstwirtschaft()) !== 0) {
      $typeName = 'landforstwirtschaft';
      $subTypeName = $type->getLandUndForstwirtschaft()[0]->getLandTyp();
    } else if (count($type->getFreizeitimmobilieGewerblich()) !== 0) {
      $typeName = 'freizeitimmobilie-gewerblich';
      $subTypeName = $type->getFreizeitimmobilieGewerblich()[0]->getFreizeitTyp();
    }
    $typeObj = Type::whereTranslation('slug', $typeName)->first();
    $subTypeObj = SubType::whereTranslation('slug', $subTypeName)->first();
    return ['type_id' => isset($typeObj) ? $typeObj->id : NULL, 'sub_type_id' => isset($subTypeObj) ? $subTypeObj->id : NULL];
  }
}
