<?php namespace App\Core\V201\Element\Activity;

use App\Core\Elements\BaseElement;
use Illuminate\Support\Collection;

/**
 * Class Transaction
 * @package App\Core\V201\Element\Activity
 */
class Transaction extends BaseElement
{
    /**
     * @return transaction form
     */
    public function getForm()
    {
        return 'App\Core\V201\Forms\Activity\Transactions\Transactions';
    }

    /**
     * @return transaction repository
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\Transaction');
    }

    /**
     * @param $transactions
     * @return array
     */
    public function getXmlData(Collection $transactions)
    {
        $transactionData = [];

        foreach ($transactions as $totalTransaction) {
            $transaction = $totalTransaction->transaction;

            $sector = [];

            foreach (getVal($transaction, ['sector'] ) as $sectorData) {
                if ($sectorData) {
                    $vocabulary = getVal($sectorData, ['sector_vocabulary']);
                    if ($vocabulary == 1) {
                        $sectorValue = getVal($sectorData, ['sector_code']);
                    } elseif ($vocabulary == 2) {
                        $sectorValue = getVal($sectorData, ['sector_category_code']);
                    } elseif ($vocabulary == "") {
                        $sectorValue = getVal($sectorData, ['sector_code']);
                    } else {
                        $sectorValue = getVal($sectorData, ['sector_text']);
                    }

                    $sector[] = [
                        '@attributes' => [
                            'vocabulary'     => $vocabulary,
                            'vocabulary-uri' => getVal($sectorData, ['vocabulary_uri']),
                            'code'           => $sectorValue
                        ],
                        'narrative'   => $this->buildNarrative(getVal($sectorData, ['narrative'], []))
                    ];
                }
            }

            $recipientCountry = [];
            if (getVal($transaction, ['recipient_country'])) {
                $recipientCountry = [
                    '@attributes' => [
                        'code' => $transaction['recipient_country'][0]['country_code']
                    ],
                    'narrative'   => $this->buildNarrative(getVal($transaction, ['recipient_country', 0, 'narrative'], []))
                ];
            }

            $recipientRegion = [];
            if (getVal($transaction, ['recipient_region'])) {
                $recipientRegion = [
                    '@attributes' => [
                        'code'       => $transaction['recipient_region'][0]['region_code'],
                        'vocabulary' => $transaction['recipient_region'][0]['vocabulary'],
                    ],
                    'narrative'   => $this->buildNarrative(getVal($transaction, ['recipient_region', 0, 'narrative'], []))
                ];
            }

            $transactionData[] = [
                '@attributes'          => [
                    'ref' => $transaction['reference']
                ],
                'transaction-type'     => [
                    '@attributes' => [
                        'code' => $transaction['transaction_type'][0]['transaction_type_code']
                    ]
                ],
                'transaction-date'     => [
                    '@attributes' => [
                        'iso-date' => $transaction['transaction_date'][0]['date']
                    ]
                ],
                'value'                => [
                    '@attributes' => [
                        'currency'   => $transaction['value'][0]['currency'],
                        'value-date' => $transaction['value'][0]['date']
                    ],
                    '@value'      => $transaction['value'][0]['amount']
                ],
                'description'          => [
                    'narrative' => $this->buildNarrative(getVal($transaction, ['description', 0, 'narrative'], []))
                ],
                'provider-org'         => [
                    '@attributes' => [
                        'ref'                  => $transaction['provider_organization'][0]['organization_identifier_code'],
                        'provider-activity-id' => $transaction['provider_organization'][0]['provider_activity_id']
                    ],
                    'narrative'   => $this->buildNarrative(getVal($transaction, ['provider_organization', 0, 'narrative'], []))
                ],
                'receiver-org'         => [
                    '@attributes' => [
                        'ref'                  => $transaction['receiver_organization'][0]['organization_identifier_code'],
                        'receiver-activity-id' => $transaction['receiver_organization'][0]['receiver_activity_id']
                    ],
                    'narrative'   => $this->buildNarrative(getVal($transaction, ['receiver_organization', 0, 'narrative'], []))
                ],
                'disbursement-channel' => [
                    '@attributes' => [
                        'code' => getVal($transaction, ['disbursement_channel', 0, 'disbursement_channel_code'])
                    ]
                ],
                'sector'               => $sector,
                'recipient-country'    => $recipientCountry,
                'recipient-region'     => $recipientRegion,
                'flow-type'            => [
                    '@attributes' => [
                        'code' => getVal($transaction, ['flow_type', 0, 'flow_type']),
                    ]
                ],
                'finance-type'         => [
                    '@attributes' => [
                        'code' => getVal($transaction, ['finance_type', 0, 'finance_type']),
                    ]
                ],
                'aid-type'             => [
                    '@attributes' => [
                        'code' => getVal($transaction, ['aid_type', 0, 'aid_type']),
                    ]
                ],
                'tied-status'          => [
                    '@attributes' => [
                        'code' => getVal($transaction, ['tied_status', 0, 'tied_status_code']),
                    ]
                ]
            ];
        }

        return $transactionData;
    }
}
