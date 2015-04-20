<?php
class Cammino_Clearsale_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_CREDITCARD_METHOD = 'payment_services/clearsale/credicard_method';
    const XML_PATH_BILLET_METHOD = 'payment_services/clearsale/billet_method';
    const XML_PATH_TRANSFER_METHOD = 'payment_services/clearsale/transfer_method';
    const XML_PATH_ENVIRONMENT = 'payment_services/clearsale/environment';
    const XML_PATH_KEY = 'payment_services/clearsale/key';
    const XML_PATH_NEIGHBORHOOD = 'payment_services/clearsale/neighborhood';
    const XML_PATH_COMPLEMENT = 'payment_services/clearsale/complement';

    public function conf($code,$store = null)
    {
        return Mage::getStoreConfig($code, $store);
    }

    public function getCreditCard($store = null)
    {
        return $this->conf(self::XML_PATH_CREDITCARD_METHOD, $store);
    }

    public function getBillet($store = null)
    {
        return $this->conf(self::XML_PATH_BILLET_METHOD, $store);
    }

    public function getTransfer($store = null)
    {
        return $this->conf(self::XML_PATH_TRANSFER_METHOD, $store);
    }

    public function getEnvironment($store = null)
    {
        $environment = $this->conf(self::XML_PATH_ENVIRONMENT, $store);

        if ($environment == 'homolog') {
            $environment = 'http://homolog.clearsale.com.br/start/Entrada/EnviarPedido.aspx';
        }else{
            $environment = 'http://www.clearsale.com.br/start/Entrada/EnviarPedido.aspx';
        }

        return $environment;
    }

    public function getKey($store = null)
    {
        return $this->conf(self::XML_PATH_KEY, $store);
    }

    public function getNeighborhood($store = null)
    {
        return $this->conf(self::XML_PATH_NEIGHBORHOOD, $store);
    }

    public function getComplement($store = null)
    {
        return $this->conf(self::XML_PATH_COMPLEMENT, $store);
    }

    public function serializeData($data)
    {
        $str = "";

        foreach($data as $key => $value) {
            $str .= $key . "=" . $value . "&";
        }

        return $str;
    }

    public function getScoreUrl($order)
    {
        $payment = $order->getPayment();
        $addata = unserialize($payment->getData('cammino_clearsale_data'));

        if ($addata["clearsale"] != "exported") {
            $addata["clearsale"] = "exported";
            $payment->setAdditionalInformation('cammino_clearsale_data', serialize($addata))->save();
        }

        $url = "{$this->getEnvironment()}?codigoIntegracao={$this->getKey()}&PedidoID={$order->getRealOrderId()}";

        return $url;
    }

    public function getType($paymentCode, $paymentData)
    {
        if (in_array($paymentCode, explode(',', $this->getCreditCard()))) {

            $paymentType = 1;
            $cardType = 4;

            if (strripos($paymentData, "diners") !== false){
                $cardType = 1;
            }

            if (strripos($paymentData, "mastercard") !== false){
                $cardType = 2;
            }

            if (strripos($paymentData, "visa") !== false){
                $cardType = 3;
            }

            if ((strripos($paymentData, "amex") !== false) || (strripos($paymentData, "american express") !== false)){
                $cardType = 5;
            }

            if (strripos($paymentData, "hipercard") !== false){
                $cardType = 6;
            }

            if (strripos($paymentData, "aura") !== false){
                $cardType = 7;
            }

            if (strripos($paymentData, "carrefour") !== false){
                $cardType = 8;
            }

        }elseif (in_array($paymentCode, explode(',', $this->getBillet()))) {
                    $paymentType = 2;
        }elseif (in_array($paymentCode, explode(',', $this->getTransfer()))) {
                    $paymentType = 6;
        }else {
            $paymentType = 0;
            $cardType = 0;
        }

        return array( 'payment' => $paymentType, 'card' => $cardType);
    }
}
