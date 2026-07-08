<!-- The Modal -->

<div class="modal fade p-0" id="transportModal" tabindex="-1" role="dialog" aria-labelledby="transportModalLabel" aria-hidden="true" style="padding-right:0px !important">
<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body special-d-flex">

        <div style="padding: 0 15px">
            <div class="modal-text-title" style="margin-bottom:20px">
                <h2>{l s='Sales Conditions' d='Shop.Theme.Catalog'}</h2>
            </div>
            <div class="modal-text-content">
                <div>
                    {if $language.id == 1}
                        <div>
                            <div class="modal-subtitle">PROCESO DE COMPRA</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Los productos que muestran su precio por m2 se venden por cajas, no siendo siempre 1 caja igual a 1 metro cuadrado. Las cantidades se redondean en el proceso de compra a cajas enteras.</li>
                                        <li>Las características técnicas de cada producto se muestran en la ficha de producto.</li>
                                        <li>Para dudas sobre el proceso puede contactarnos por teléfono o Whatsapp al número +34 647 145 062 o por correo a atencion.es@ceramicconnection.es</li>
                                        <li>Disponibilidad: todos nuestros productos suelen estar en stock, pero puede darse el caso que alguna referencia esté fuera de stock temporalmente, por lo que <strong>se recomienda mirar la disponibilidad en la ficha de producto</strong> y en caso de dudas, consultar con atención al cliente.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">PLAZOS DE ENTREGA</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Los plazos de entrega <strong>varían por producto.</strong> En la ficha de cada producto se puede observar el plazo de entrega de cada modelo.</li>
                                        <li>Los plazos de entrega <strong>son orientativos</strong>, dependiendo en última instancia de la gestión logística por parte de la empresa transportista y de la facilidad para coordinar la entrega con el cliente.</li>
                                        <li>Durante las Navidades (del 24 de Diciembre al 7 de Enero) y en Agosto, <strong>los plazos de entrega se pueden ver ampliados debido a vacaciones de personal logístico.</strong></li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">RECEPCIÓN DEL PEDIDO</div>
                            <div>
                                <p>
                                    <ul>
                                        <li><strong>La empresa transportista</strong> deberá concertar con el cliente el horario para la entrega.</li>
                                        <li>Los pedidos se entregan mediante camión plataforma, apoyado por un operario con una transpaleta. <strong>Se entregarán a pié de calle lo más cerca posible de la puerta.</strong></li>
                                        <li>Al recibir el pedido, es necesario verificar que el material haya llegado correctamente. Revisar el palet en busca de golpes. <strong>Importantísimo hacer fotos del palet antes de desembalarlo y de cualquier desperfecto identificado.</strong> En caso de algún incidente se debe dejar por escrito en el albarán del repartidor para poder tramitar la reclamación.</li>
                                        <li>Es muy recomendable <strong>hacer fotos o videos del pedido en el momento de su recepción aún si se ve en buenas condiciones</strong>, ya que en ocasiones la empresa transportista puede haber manipulado el pedido y producido desperfectos. Esto facilitará cualquier gestión posterior de reclamación en caso de que fuera necesaria.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">INCIDENCIAS EN EL ENVÍO</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>En caso de rotura o desperfecto durante el transporte, deberá hacerse constar en el albarán del transportista en el momento de la entrega.<strong> Si el daño es considerable, es recomendable rechazar la mercancía.</strong> Ceramic Connection procederá al envío de la reposición de manera inmediata.</li>
                                        <li><strong>Es necesario revisar la mercancía antes de manipularla.</strong> Se dispone de 48 horas para poder indicar daños ocultos en la mercancía producidos durante el transporte.</li>
                                        <li>Será necesaria la presentación de fotografías o vídeos del problema antes de cualquier manipulación.</li>
                                        <li>Una vez tramitada la solicitud, si procede, Ceramic Connection gestionará la recogida con la empresa de transporte.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    {/if}

                    {if $language.id == 2}
                        <div>
                            <div class="modal-subtitle">PROCESSUS D'ACHAT</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Les produits affichant leur prix au m² sont vendus par cartons, une boîte ne correspondant pas toujours à un mètre carré. Les quantités sont arrondies à des cartons complets lors du processus d'achat.</li>
                                        <li>Les caractéristiques techniques de chaque produit sont indiquées sur la fiche du produit.</li>
                                        <li>Pour toute question sur le processus, vous pouvez nous contacter par téléphone ou WhatsApp au +34 647 145 062 ou par e-mail à atencion.es@ceramicconnection.es</li>
                                        <li>Disponibilité : tous nos produits sont généralement en stock, mais il se peut qu'une référence soit temporairement en rupture. <strong>Il est donc recommandé de vérifier la disponibilité sur la fiche produit</strong> et, en cas de doute, de contacter le service client.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">DÉLAIS DE LIVRAISON</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Les délais de livraison <strong>varient selon le produit.</strong> Ils sont indiqués sur la fiche de chaque modèle.</li>
                                        <li>Les délais de livraison sont <strong>indicatifs</strong> et dépendent en dernière instance de la gestion logistique du transporteur et de la coordination avec le client.</li>
                                        <li>Pendant les fêtes de fin d'année (du 24 décembre au 7 janvier) et en août, <strong>les délais de livraison peuvent être prolongés en raison des congés du personnel logistique.</strong></li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">RÉCEPTION DE LA COMMANDE</div>
                            <div>
                                <p>
                                    <ul>
                                        <li><strong>Le transporteur</strong> doit convenir d'un horaire de livraison avec le client.</li>
                                        <li>Les commandes sont livrées par camion avec hayon et transpalette. <strong>La livraison se fera en bordure de rue, aussi près que possible de la porte.</strong></li>
                                        <li>À la réception de la commande, il est nécessaire de vérifier que le matériel est arrivé en bon état. Inspectez la palette pour détecter tout choc. <strong>Il est très important de prendre des photos de la palette avant le déballage et de tout dommage visible.</strong> En cas d'incident, il faut le noter sur le bon de livraison pour permettre la réclamation.</li>
                                        <li>Il est fortement recommandé de <strong>prendre des photos ou vidéos de la commande au moment de la réception, même si elle semble en bon état</strong>, car le transporteur peut parfois avoir causé des dommages. Cela facilitera toute réclamation ultérieure.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">INCIDENTS DE LIVRAISON</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>En cas de casse ou dommage pendant le transport, cela doit être indiqué sur le bon de livraison du transporteur au moment de la réception. <strong>Si le dommage est important, il est conseillé de refuser la marchandise.</strong> Ceramic Connection enverra immédiatement un remplacement.</li>
                                        <li><strong>Il est nécessaire de vérifier la marchandise avant toute manipulation.</strong> Vous disposez de 48 heures pour signaler tout dommage caché causé pendant le transport.</li>
                                        <li>Des photos ou vidéos du problème doivent être fournies avant toute manipulation.</li>
                                        <li>Une fois la demande validée, si applicable, Ceramic Connection organisera la collecte avec le transporteur.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                    {/if}

                    {if $language.id == 3}
                        <div>
                            <div class="modal-subtitle">PURCHASE PROCESS</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Products showing their price per m² are sold by boxes, and one box is not always equal to one square meter. Quantities are rounded to full boxes during the purchase process.</li>
                                        <li>The technical specifications of each product are shown on the product sheet.</li>
                                        <li>For questions about the process, you can contact us by phone or WhatsApp at +34 647 145 062 or by email at atencion.es@ceramicconnection.es</li>
                                        <li>Availability: all our products are usually in stock, but some items may be temporarily out of stock. <strong>It is recommended to check availability on the product sheet</strong> and, in case of doubt, contact customer service.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">DELIVERY TIMES</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Delivery times <strong>vary by product.</strong> Each product sheet shows the estimated delivery time.</li>
                                        <li>Delivery times are <strong>approximate</strong> and ultimately depend on the logistics management of the carrier and coordination with the customer.</li>
                                        <li>During Christmas (December 24 to January 7) and August, <strong>delivery times may be extended due to logistics staff holidays.</strong></li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">ORDER RECEIPT</div>
                            <div>
                                <p>
                                    <ul>
                                        <li><strong>The transport company</strong> must arrange the delivery time with the customer.</li>
                                        <li>Orders are delivered by platform truck with a pallet jack. <strong>They will be delivered at street level, as close to the entrance as possible.</strong></li>
                                        <li>Upon receiving the order, check that the material has arrived correctly. Inspect the pallet for any damage. <strong>It is very important to take photos of the pallet before unpacking and of any defects found.</strong> Any incident should be noted on the delivery slip to process the claim.</li>
                                        <li>It is highly recommended to <strong>take photos or videos of the order at the time of delivery, even if it appears in good condition</strong>, as handling during transport may cause damage. This will help in any future claim process if needed.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">SHIPPING INCIDENTS</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>In case of breakage or damage during transport, it must be noted on the delivery slip at the time of receipt. <strong>If the damage is significant, it is recommended to refuse the goods.</strong> Ceramic Connection will immediately send a replacement.</li>
                                        <li><strong>The goods must be checked before handling.</strong> You have 48 hours to report hidden damage caused during transport.</li>
                                        <li>Photos or videos of the issue must be provided before any handling.</li>
                                        <li>Once the claim is processed, if applicable, Ceramic Connection will arrange collection with the transport company.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                    {/if}

                    {if $language.id == 4}
                        <div>
                            <div class="modal-subtitle">KAUFPROZESS</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Produkte, die ihren Preis pro m² anzeigen, werden kartonweise verkauft; ein Karton entspricht nicht immer einem Quadratmeter. Die Mengen werden während des Kaufprozesses auf volle Kartons gerundet.</li>
                                        <li>Die technischen Merkmale jedes Produkts sind im Produktblatt angegeben.</li>
                                        <li>Bei Fragen zum Prozess können Sie uns telefonisch oder per WhatsApp unter +34 647 145 062 oder per E-Mail an atencion.es@ceramicconnection.es kontaktieren.</li>
                                        <li>Verfügbarkeit: Unsere Produkte sind in der Regel auf Lager, jedoch kann es vorkommen, dass einige Referenzen vorübergehend nicht verfügbar sind. <strong>Es wird empfohlen, die Verfügbarkeit im Produktblatt zu prüfen</strong> und bei Zweifeln den Kundendienst zu kontaktieren.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">LIEFERZEITEN</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Die Lieferzeiten <strong>variieren je nach Produkt.</strong> Auf dem Produktblatt jedes Modells ist die jeweilige Lieferzeit angegeben.</li>
                                        <li>Die Lieferzeiten sind <strong>Richtwerte</strong> und hängen letztlich von der Logistik des Transportunternehmens und der Abstimmung mit dem Kunden ab.</li>
                                        <li>Während der Weihnachtszeit (24. Dezember bis 7. Januar) und im August <strong>können sich die Lieferzeiten aufgrund von Betriebsferien verlängern.</strong></li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">EMPFANG DER BESTELLUNG</div>
                            <div>
                                <p>
                                    <ul>
                                        <li><strong>Das Transportunternehmen</strong> muss mit dem Kunden einen Liefertermin vereinbaren.</li>
                                        <li>Die Bestellungen werden per Lkw mit Plattform und Hubwagen geliefert. <strong>Die Lieferung erfolgt auf Straßenniveau, so nah wie möglich an der Tür.</strong></li>
                                        <li>Beim Empfang der Bestellung muss überprüft werden, ob das Material unbeschädigt angekommen ist. Überprüfen Sie die Palette auf Schäden. <strong>Es ist sehr wichtig, Fotos der Palette vor dem Auspacken und von eventuellen Schäden zu machen.</strong> Im Falle eines Vorfalls muss dies auf dem Lieferschein vermerkt werden, um eine Reklamation zu bearbeiten.</li>
                                        <li>Es wird dringend empfohlen, <strong>Fotos oder Videos der Lieferung beim Empfang zu machen, auch wenn sie unbeschädigt erscheint</strong>, da der Transport Schäden verursacht haben kann. Dies erleichtert spätere Reklamationen.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">VERSANDVORFÄLLE</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Bei Bruch oder Beschädigung während des Transports muss dies auf dem Lieferschein des Fahrers vermerkt werden. <strong>Bei größeren Schäden wird empfohlen, die Ware abzulehnen.</strong> Ceramic Connection sendet umgehend Ersatz.</li>
                                        <li><strong>Die Ware muss vor dem Umgang überprüft werden.</strong> Sie haben 48 Stunden Zeit, um verdeckte Transportschäden zu melden.</li>
                                        <li>Fotos oder Videos des Problems müssen vor jeder Handhabung vorgelegt werden.</li>
                                        <li>Sobald die Anfrage bearbeitet ist, organisiert Ceramic Connection gegebenenfalls die Abholung mit dem Transportunternehmen.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                    {/if}

                    {if $language.id == 5}
                        <div>
                            <div class="modal-subtitle">PROCESSO DE COMPRA</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Os produtos com preço por m² são vendidos por caixas, não sendo sempre 1 caixa igual a 1 metro quadrado. As quantidades são arredondadas para caixas inteiras durante o processo de compra.</li>
                                        <li>As características técnicas de cada produto estão indicadas na ficha do produto.</li>
                                        <li>Para dúvidas sobre o processo, entre em contato connosco por telefone ou WhatsApp pelo +34 647 145 062 ou por e-mail em atencion.es@ceramicconnection.es</li>
                                        <li>Disponibilidade: todos os nossos produtos costumam estar em stock, mas pode acontecer que alguma referência esteja temporariamente indisponível, por isso <strong>recomenda-se verificar a disponibilidade na ficha do produto</strong> e, em caso de dúvida, contactar o atendimento ao cliente.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">PRAZOS DE ENTREGA</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Os prazos de entrega <strong>variam conforme o produto.</strong> Na ficha de cada produto é indicado o prazo correspondente.</li>
                                        <li>Os prazos de entrega são <strong>orientativos</strong>, dependendo da gestão logística da transportadora e da coordenação com o cliente.</li>
                                        <li>Durante o Natal (de 24 de dezembro a 7 de janeiro) e em agosto, <strong>os prazos de entrega podem ser ampliados devido às férias da equipa logística.</strong></li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">RECEÇÃO DO PEDIDO</div>
                            <div>
                                <p>
                                    <ul>
                                        <li><strong>A transportadora</strong> deve combinar com o cliente o horário de entrega.</li>
                                        <li>Os pedidos são entregues por camião com plataforma e empilhador. <strong>A entrega será feita ao nível da rua, o mais próximo possível da porta.</strong></li>
                                        <li>Ao receber o pedido, é necessário verificar se o material chegou corretamente. Verifique o palete à procura de danos. <strong>É muito importante tirar fotos do palete antes de o desembalar e de qualquer dano identificado.</strong> Em caso de incidente, deve-se registá-lo na guia de entrega para possibilitar a reclamação.</li>
                                        <li>É altamente recomendável <strong>tirar fotos ou vídeos do pedido no momento da receção, mesmo que pareça em bom estado</strong>, pois a transportadora pode ter causado danos. Isso facilitará qualquer processo de reclamação posterior.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">INCIDENTES NO ENVIO</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Em caso de quebra ou dano durante o transporte, isso deve ser indicado na guia do transportador no momento da entrega. <strong>Se o dano for significativo, recomenda-se recusar a mercadoria.</strong> A Ceramic Connection enviará a substituição imediatamente.</li>
                                        <li><strong>É necessário verificar a mercadoria antes de manuseá-la.</strong> Dispõe de 48 horas para comunicar danos ocultos causados durante o transporte.</li>
                                        <li>Será necessário apresentar fotografias ou vídeos do problema antes de qualquer manuseamento.</li>
                                        <li>Após a validação do pedido, se aplicável, a Ceramic Connection coordenará a recolha com a transportadora.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                    {/if}

                    {if $language.id == 6}
                        <div>
                            <div class="modal-subtitle">AANKOOPPROCES</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>Producten met een prijs per m² worden per doos verkocht, waarbij één doos niet altijd gelijk is aan één vierkante meter. De hoeveelheden worden tijdens het aankoopproces afgerond op hele dozen.</li>
                                        <li>De technische kenmerken van elk product staan vermeld op de productpagina.</li>
                                        <li>Voor vragen over het proces kunt u contact met ons opnemen via telefoon of WhatsApp op +34 647 145 062 of per e-mail op atencion.es@ceramicconnection.es</li>
                                        <li>Beschikbaarheid: al onze producten zijn meestal op voorraad, maar sommige referenties kunnen tijdelijk uitverkocht zijn. <strong>Het wordt aanbevolen om de beschikbaarheid op de productpagina te controleren</strong> en bij twijfel contact op te nemen met de klantenservice.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">LEVERTIJDEN</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>De levertijden <strong>verschillen per product.</strong> Op de productpagina van elk model wordt de geschatte levertijd weergegeven.</li>
                                        <li>De levertijden zijn <strong>indicatief</strong> en hangen uiteindelijk af van de logistieke organisatie van de vervoerder en de coördinatie met de klant.</li>
                                        <li>Tijdens Kerst (24 december tot 7 januari) en in augustus <strong>kunnen de levertijden langer zijn vanwege vakantieperiodes van het logistieke personeel.</strong></li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">ONTVANGST VAN DE BESTELLING</div>
                            <div>
                                <p>
                                    <ul>
                                        <li><strong>Het transportbedrijf</strong> moet met de klant een levertijd afspreken.</li>
                                        <li>Bestellingen worden geleverd met een vrachtwagen met laadklep en palletwagen. <strong>De levering gebeurt op straatniveau, zo dicht mogelijk bij de ingang.</strong></li>
                                        <li>Bij ontvangst van de bestelling moet worden gecontroleerd of het materiaal in goede staat is aangekomen. Controleer de pallet op schade. <strong>Het is zeer belangrijk om foto’s van de pallet te maken vóór het uitpakken en van eventuele schade.</strong> Noteer eventuele incidenten op de leveringsbon voor het indienen van een klacht.</li>
                                        <li>Het wordt sterk aanbevolen om <strong>foto’s of video’s van de bestelling te maken bij ontvangst, zelfs als deze er goed uitziet</strong>, aangezien de vervoerder mogelijk schade heeft veroorzaakt. Dit zal het afhandelen van eventuele klachten vergemakkelijken.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <div class="modal-subtitle">VERZENDINCIDENTEN</div>
                            <div>
                                <p>
                                    <ul>
                                        <li>In geval van breuk of schade tijdens transport moet dit op het leveringsbewijs van de chauffeur worden vermeld. <strong>Als de schade aanzienlijk is, wordt aanbevolen de goederen te weigeren.</strong> Ceramic Connection zal onmiddellijk een vervanging verzenden.</li>
                                        <li><strong>De goederen moeten vóór behandeling worden gecontroleerd.</strong> U heeft 48 uur om verborgen transportschade te melden.</li>
                                        <li>Foto’s of video’s van het probleem moeten worden overlegd vóór enige behandeling.</li>
                                        <li>Nadat het verzoek is verwerkt, zal Ceramic Connection, indien van toepassing, de ophaling met de vervoerder regelen.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                    {/if}
                </div>
            </div>
        </div>
    </div>
    <!-- div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
    </div -->
    </div>
</div>
</div>
            
<!-- End Modal -->