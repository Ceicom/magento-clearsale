<?php
class Cammino_Clearsale_Helper_DataTest extends PHPUnit_Framework_TestCase
{

    protected $dataClass;

    public function setUp()
    {
       $this->dataClass = Mage::helper('cammino_clearsale');
    }

    public function testDataClass()
    {
        $this->assertInstanceOf('Cammino_Clearsale_Helper_Data', $this->dataClass);
    }
}
