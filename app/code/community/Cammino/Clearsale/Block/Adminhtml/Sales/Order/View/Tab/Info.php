<?php
class Cammino_Clearsale_Block_Adminhtml_Sales_Order_View_Tab_Info extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Info
{

    protected $url;
    protected $orderId;

    public function getPaymentHtml()
    {
        $order = $this->getOrder();
        $html = parent::getPaymentHtml();
        $helper = Mage::helper('cammino_clearsale');

        $payment = $order->getPayment();
        $type = $helper->getType($payment->getMethodInstance()->getCode(), $payment->getData("cammino_clearsale_data"));

        if ($type['payment'] && $order->getState() != "canceled") {

            $this->url = $helper->getScoreUrl($order);
            $this->orderId = $order->getRealOrderId();


    $html .= $this->setTemplate('cammino/clearsale/info.phtml')->toHtml();
        }

        return $html;
    }

    public function getIframeUrl()
    {
        return $this->url;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
}
