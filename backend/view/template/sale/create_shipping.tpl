<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://dpd.com/common/service/types/Authentication/2.0" xmlns:ns1="http://dpd.com/common/service/types/ShipmentService/4.4">
            <soapenv:Header>
            <ns:authentication>
            <delisId><?php echo $username;?></delisId>
            <authToken><?php echo $token  ?></authToken>
            <messageLanguage><?php echo $messageLanguage; ?></messageLanguage>
            </ns:authentication>
            </soapenv:Header>
            <soapenv:Body>
            <ns1:storeOrders>
            <printOptions>
            <printOption>
            <outputFormat>PDF</outputFormat>
            <paperFormat>A4</paperFormat>
            </printOption>
            <splitByParcel><?php echo $splitByParcel; ?></splitByParcel>
            </printOptions>
            <order>
            <generalShipmentData>
            <mpsCustomerReferenceNumber1><?php echo $data['mpsCustomerReferenceNumber1'];?></mpsCustomerReferenceNumber1>
            <identificationNumber><?php echo $data['identificationNumber'];?></identificationNumber>
            <sendingDepot><?php echo $data['sendingDepot'];?></sendingDepot>
            <product><?php echo $data['product'];?></product>
            <mpsWeight><?php echo $data['mpsWeight'];?></mpsWeight>
            <sender>
            <name1><?php echo $sender['name1']; ?></name1>
            <name2><?php echo $sender['name2']; ?></name2>
            <street><?php echo $sender['street']; ?></street>
            <houseNo><?php echo $sender['houseNo']; ?></houseNo>
            <country><?php echo $sender['country']; ?></country>
            <zipCode><?php echo $sender['zipCode']; ?></zipCode>
            <city><?php echo $sender['city']; ?></city>
            </sender>
            <recipient>
            <name1><?php echo $recipient['name1'];?></name1>
            <?php if(!empty($recipient['name2'])): ?>
            <name2><?php echo $recipient['name2'] ?></name2>
            <?php endif; ?>
            <street><?php echo $recipient['street']?></street>
            <houseNo><?php echo $recipient['houseNo']?></houseNo>
            <country><?php echo $recipient['country']?></country>
            <zipCode><?php echo $recipient['zipCode']?></zipCode>
            <city><?php echo $recipient['city']?></city>
            </recipient>
            </generalShipmentData>
            <parcels>            
            <customerReferenceNumber1><?php echo $parcels['customerReferenceNumber1'] ?></customerReferenceNumber1>
            <weight><?php echo $parcels['weight'] ?></weight>            
            </parcels>
            <productAndServiceData>
            <orderType><?php echo $productAndServiceData['orderType'];?></orderType>
            </productAndServiceData>
            </order>
            </ns1:storeOrders>
            </soapenv:Body>
            </soapenv:Envelope>