<?php
class Netresearch_OPS_Test_Helper_AddressTest extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @dataProvider provideAddresses
     *
     * @param  string[] $test
     * @param  string[] $result
     */
    public function testSplitStreet($test, $result)
    {
        $this->assertEquals($result, Netresearch_OPS_Helper_Address::splitStreet($test));
    }

    public function provideAddresses()
    {
        return array(
            'Expect Fallback'            => array(
                array('Clematis Cottage, Mill Lane', 'Bartholomew'),
                array(
                    Netresearch_OPS_Helper_Address::STREET_NAME   => 'Clematis Cottage, Mill Lane',
                    Netresearch_OPS_Helper_Address::STREET_NUMBER => '',
                    Netresearch_OPS_Helper_Address::SUPPLEMENT    => 'Bartholomew'
                )
            ),
            'Street, Number, Supplement' => array(
                array('3940 Radio Road', 'Unit 110'),
                array(
                    Netresearch_OPS_Helper_Address::STREET_NAME   => 'Radio Road',
                    Netresearch_OPS_Helper_Address::STREET_NUMBER => '3940',
                    Netresearch_OPS_Helper_Address::SUPPLEMENT    => 'Unit 110'
                )
            ),
            'Street, Number'             => array(
                array('Nafarroa Kalea 9'),
                array(
                    Netresearch_OPS_Helper_Address::STREET_NAME   => 'Nafarroa Kalea',
                    Netresearch_OPS_Helper_Address::STREET_NUMBER => '9',
                    Netresearch_OPS_Helper_Address::SUPPLEMENT    => ''
                )
            ),
            'Austrian Address'           => array(
                array('Lieblgasse 2/41/7/21'),
                array(
                    Netresearch_OPS_Helper_Address::STREET_NAME   => 'Lieblgasse',
                    Netresearch_OPS_Helper_Address::STREET_NUMBER => '2/41/7/21',
                    Netresearch_OPS_Helper_Address::SUPPLEMENT    => ''
                )
            )
        );
    }
}

