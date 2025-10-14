<!-- The Modal -->

<div class="modal fade p-0" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true" style="padding-right:0px !important">
<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body special-d-flex">
        
        <div class="modal-text">
            <div class="modal-text-title">
                <h2>{l s='Payment method' d='Shop.Theme.Catalog'}</h2>
            </div>
            <div class="modal-text-content">
                {if $language.id==1}
                    <div>
                        <div class="modal-subtitle">TARGETA DE CRÉDITO O DÉBITO</div>
                        <div>
                            <p>
                                <ul>
                                    <li>El pago se procesa a través de la pasarela segura <strong>Redsys</strong>, que acepta la mayoría de tarjetas.</li>
                                    <li>Redsys garantiza la <strong>protección de los datos mediante cifrado SSL.</strong></li>
                                    <li>El cargo se efectuará únicamente tras verificar los datos y recibir autorización del emisor.</li>
                                    <li>Los datos se utilizarán exclusivamente para completar la transacción o tramitar un posible reembolso.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">PAYPAL</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Disponible el pago seguro con Paypal mediante la propia plataforma de Paypal</li>
                                    <li>Existe la opción de realizar un pago en 3 plazos.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">TRANSFERENCIA BANCARIA</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Una vez realizado el pedido, este quedará <strong>pendiente de pago</strong> hasta recibir la transferencia.</li>
                                    <li>Es imprescindible indicar el <strong>número de pedido</strong> en el concepto de la transferencia.</li>
                                    <li>El pedido será procesado una vez confirmado el pago.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">BIZUM</div>
                        <div>
                            <p>
                                <ul>
                                    <li>También se encuentra habilitado el <strong>pago mediante Bizum</strong>, de forma rápida y segura.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                {/if}

                {if $language.id==2}
                    <div>
                        <div class="modal-subtitle">CARTE DE CRÉDIT OU DE DÉBIT</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Le paiement est traité via la passerelle sécurisée <strong>Redsys</strong>, acceptant la plupart des cartes.</li>
                                    <li>Redsys garantit la <strong>protection des données grâce au cryptage SSL.</strong></li>
                                    <li>Le débit ne sera effectué qu'après vérification des informations et autorisation de l'émetteur.</li>
                                    <li>Les données seront utilisées uniquement pour compléter la transaction ou traiter un éventuel remboursement.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">PAYPAL</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Paiement sécurisé disponible avec PayPal via la plateforme officielle de PayPal.</li>
                                    <li>Il existe une option pour effectuer un paiement en 3 fois.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>


                    <div>
                        <div class="modal-subtitle">VIREMENT BANCAIRE</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Après avoir passé commande, celle-ci restera <strong>en attente de paiement</strong> jusqu'à réception du virement.</li>
                                    <li>Il est indispensable d'indiquer le <strong>numéro de commande</strong> dans le libellé du virement.</li>
                                    <li>La commande sera traitée après confirmation du paiement.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">BIZUM</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Le <strong>paiement via Bizum</strong> est également disponible, rapide et sécurisé.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                {/if}

                {if $language.id==3}
                    <div>
                        <div class="modal-subtitle">CREDIT OR DEBIT CARD</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Payments are processed through the secure gateway <strong>Redsys</strong>, accepting most cards.</li>
                                    <li>Redsys ensures <strong>data protection via SSL encryption.</strong></li>
                                    <li>The charge will only be made after verifying the details and receiving authorization from the issuer.</li>
                                    <li>Data will only be used to complete the transaction or process a possible refund.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">PAYPAL</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Secure payment available through PayPal using PayPal’s own platform.</li>
                                    <li>There is an option to make a payment in 3 installments.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>


                    <div>
                        <div class="modal-subtitle">BANK TRANSFER</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Once the order is placed, it will remain <strong>pending payment</strong> until the transfer is received.</li>
                                    <li>It is essential to indicate the <strong>order number</strong> in the transfer reference.</li>
                                    <li>The order will be processed once payment is confirmed.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">BIZUM</div>
                        <div>
                            <p>
                                <ul>
                                    <li><strong>Bizum payment</strong> is also available for fast and secure transactions.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                {/if}

                {if $language.id==4}
                    <div>
                        <div class="modal-subtitle">KREDIT- ODER DEBITKARTE</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Die Zahlung wird über das sichere Gateway <strong>Redsys</strong> abgewickelt, das die meisten Karten akzeptiert.</li>
                                    <li>Redsys garantiert den <strong>Daten­schutz durch SSL-Verschlüsselung.</strong></li>
                                    <li>Die Abbuchung erfolgt erst nach Prüfung der Daten und Genehmigung durch den Kartenanbieter.</li>
                                    <li>Daten werden ausschließlich zur Abwicklung der Transaktion oder zur Bearbeitung einer möglichen Rückerstattung verwendet.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">PAYPAL</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Sichere Zahlung mit PayPal über die offizielle PayPal-Plattform verfügbar.</li>
                                    <li>Es besteht die Möglichkeit, die Zahlung in 3 Raten zu leisten.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>


                    <div>
                        <div class="modal-subtitle">BANKÜBERWEISUNG</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Nach Aufgabe der Bestellung bleibt diese <strong>zahlungsbedingt offen</strong>, bis die Überweisung eingegangen ist.</li>
                                    <li>Bitte geben Sie unbedingt die <strong>Bestellnummer</strong> im Verwendungszweck an.</li>
                                    <li>Die Bestellung wird nach Bestätigung der Zahlung bearbeitet.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">BIZUM</div>
                        <div>
                            <p>
                                <ul>
                                    <li><strong>BIZUM-Zahlung</strong> ist ebenfalls möglich – schnell und sicher.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                {/if}

                {if $language.id==5}
                    <div>
                        <div class="modal-subtitle">CARTÃO DE CRÉDITO OU DÉBITO</div>
                        <div>
                            <p>
                                <ul>
                                    <li>O pagamento é processado através da plataforma segura <strong>Redsys</strong>, que aceita a maioria dos cartões.</li>
                                    <li>A Redsys garante a <strong>proteção de dados através de criptografia SSL.</strong></li>
                                    <li>A cobrança só será efetuada após verificação dos dados e autorização do emissor.</li>
                                    <li>Os dados serão usados exclusivamente para concluir a transação ou processar um possível reembolso.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">PAYPAL</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Pagamento seguro disponível com PayPal através da própria plataforma do PayPal.</li>
                                    <li>Existe a opção de efetuar o pagamento em 3 prestações.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>


                    <div>
                        <div class="modal-subtitle">TRANSFERÊNCIA BANCÁRIA</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Após fazer a encomenda, esta ficará <strong>pendente de pagamento</strong> até receber a transferência.</li>
                                    <li>É essencial indicar o <strong>número da encomenda</strong> no conceito da transferência.</li>
                                    <li>A encomenda será processada após confirmação do pagamento.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">BIZUM</div>
                        <div>
                            <p>
                                <ul>
                                    <li>O <strong>pagamento via Bizum</strong> também está disponível, sendo rápido e seguro.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                {/if}

                {if $language.id==6}
                    <div>
                        <div class="modal-subtitle">CREDIT- OF DEBETKAART</div>
                        <div>
                            <p>
                                <ul>
                                    <li>De betaling wordt verwerkt via de veilige gateway <strong>Redsys</strong>, die de meeste kaarten accepteert.</li>
                                    <li>Redsys garandeert <strong>gegevensbescherming via SSL‑codering.</strong></li>
                                    <li>De betaling wordt pas uitgevoerd na verificatie van de gegevens en goedkeuring door de uitgever.</li>
                                    <li>Gegevens worden uitsluitend gebruikt om de transactie te voltooien of een eventuele terugbetaling te verwerken.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">PAYPAL</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Veilige betaling beschikbaar via PayPal met het officiële PayPal-platform.</li>
                                    <li>Er is een optie om in 3 termijnen te betalen.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>


                    <div>
                        <div class="modal-subtitle">BANKOVERSCHRIJVING</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Na het plaatsen van de bestelling blijft deze <strong>in afwachting van betaling</strong> totdat de overschrijving is ontvangen.</li>
                                    <li>Vermeld altijd het <strong>ordernummer</strong> bij de overschrijving.</li>
                                    <li>De bestelling wordt verwerkt zodra de betaling is bevestigd.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">BIZUM</div>
                        <div>
                            <p>
                                <ul>
                                    <li><strong>Betalen met Bizum</strong> is ook mogelijk, snel en veilig.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                {/if}

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