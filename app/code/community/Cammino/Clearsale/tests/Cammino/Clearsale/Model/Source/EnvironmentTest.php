<?php
class Cammino_Clearsale_Model_Source_EnvironmentTest extends PHPUnit_Framework_TestCase
{
    protected $environmentClass;

    public function setUp()
    {
       $this->environmentClass = Mage::getModel('cammino_clearsale/source_environment');
    }

    public function testEnvironmentClass()
    {
        $this->assertInstanceOf('Cammino_Clearsale_Model_Source_Environment', $this->environmentClass);
    }

    public function testToOptionArray()
    {
        $option = $this->environmentClass->toOptionArray();
        $this->assertContains('production', $option[0]['value']);
        $this->assertContains('homolog', $option[1]['value']);
    }
}
