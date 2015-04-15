<?php
class Cammino_Clearsale_Block_Adminhtml_Sales_Order_View_Tab_InfoTest extends PHPUnit_Framework_TestCase
{

    protected $InfoClass;

    public function setUp()
    {
        $this->InfoClass = new Cammino_Clearsale_Block_Adminhtml_Sales_Order_View_Tab_Info;
        $this->InfoClass->setLayout($this->_layout);
    }

    public function testInfoClass()
    {
        $this->assertInstanceOf('Cammino_Clearsale_Block_Adminhtml_Sales_Order_View_Tab_Info', $this->InfoClass);
    }
}
