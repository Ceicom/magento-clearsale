<?php
class Cammino_Clearsale_AdminController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('orderId');
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $helper = Mage::helper('cammino_clearsale');

        $items = $order->getAllItems();
        $payment = $order->getPayment();
        $customerModel = Mage::getModel('customer/customer');
        $customer = $customerModel->load($order->getCustomerId());
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        $billingName = $billingAddress->getFirstname() . " " . $billingAddress->getMiddlename() . " " . $billingAddress->getLastname();
        $billingName = trim(str_replace("  ", " ", $billingName));
        $billingCountry = Mage::getModel('directory/country')->loadByCode($billingAddress->getCountry());
        $billingPhone = preg_replace('/[^0-9]/', '', $billingAddress->getTelephone());

        $shippingName = $shippingAddress->getFirstname() . " " . $shippingAddress->getMiddlename() . " " . $shippingAddress->getLastname();
        $shippingName = trim(str_replace("  ", " ", $shippingName));
        $shippingCountry = Mage::getModel('directory/country')->loadByCode($shippingAddress->getCountry());
        $shippingPhone = preg_replace('/[^0-9]/', '', $shippingAddress->getTelephone());

        $type = $helper->getType($payment->getMethodInstance()->getCode(), $payment->getData("cammino_clearsale_data"));

        $data = array(
            "CodigoIntegracao" => Mage::getStoreConfig("payment_services/clearsale/key"),
            "PedidoID" => $order->getRealOrderId(),
            "Data" => date('d-m-Y H:i:s', strtotime($order->getCreatedAt())),
            "IP" => $order->getRemoteIp(),
            "Total" => number_format(floatval($order->getGrandTotal()), 2, ".", ""),
            "TipoPagamento" => $type['payment'],
            "TipoCartao" => $type['card'],
            "Cobranca_Nome" => $billingName,
            "Cobranca_Email" => $customer->getEmail(),
            "Cobranca_Documento" => preg_replace('/[^0-9]/', '', $customer->getTaxvat()),
            "Cobranca_Logradouro" => $billingAddress->getStreet(1),
            "Cobranca_Logradouro_Numero" => $billingAddress->getStreet(2),
            "Cobranca_Logradouro_Complemento" => $billingAddress->getStreet($helper->getComplement()),
            "Cobranca_Bairro" => $billingAddress->getStreet($helper->getNeighborhood()),
            "Cobranca_Cidade" => $billingAddress->getCity(),
            "Cobranca_Estado" => $billingAddress->getRegionCode(),
            "Cobranca_CEP" => preg_replace('/[^0-9]/', '', $billingAddress->getPostcode()),
            "Cobranca_Pais" => $billingCountry->getName(),
            "Cobranca_DDD_Telefone_1" => substr($billingPhone, 0, 2),
            "Cobranca_Telefone_1" => substr($billingPhone, 2, 9),
            "Entrega_Nome" => $shippingName,
            "Entrega_Logradouro" => $shippingAddress->getStreet(1),
            "Entrega_Logradouro_Numero" => $shippingAddress->getStreet(2),
            "Entrega_Logradouro_Complemento" => $shippingAddress->getStreet($helper->getComplement()),
            "Entrega_Bairro" => $shippingAddress->getStreet($helper->getNeighborhood()),
            "Entrega_Cidade" => $shippingAddress->getCity(),
            "Entrega_Estado" => $shippingAddress->getRegionCode(),
            "Entrega_CEP" => preg_replace('/[^0-9]/', '', $shippingAddress->getPostcode()),
            "Entrega_Pais" => $shippingCountry->getName(),
            "Entrega_DDD_Telefone_1" => substr($shippingPhone, 0, 2),
            "Entrega_Telefone_1" => substr($shippingPhone, 2, 9)
        );

        $itemIndex = 1;

        foreach ($items as $item) {
            $data["Item_ID_$itemIndex"] = $item->getSku();
            $data["Item_Nome_$itemIndex"] = $item->getName();
            $data["Item_Qtd_$itemIndex"] = intval($item->getQtyOrdered());
            $data["Item_Valor_$itemIndex"] = number_format(floatval($item->getPrice()), 2, ".", "");
            $itemIndex++;
        }

        print_r($data) ;

        $url = $helper->getEnvironment();
        $data = $helper->serializeData($data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);

        $returnString = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        if (!$info['http_code']) {
            return false;
        }
        return true;
    }

}
