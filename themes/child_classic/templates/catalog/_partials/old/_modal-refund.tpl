<!-- The Modal -->

<div class="modal fade p-0" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true" style="padding-right:0px !important">
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
                <h2>{l s='Returns / issues' d='Shop.Theme.Catalog'}</h2>
            </div>
            <div class="modal-text-content">
                {if $language.id==1}
                   <div>
                        <div class="modal-subtitle">PLAZOS Y CONDICIONES GENERALES</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Las devoluciones pueden solicitarse dentro de los <strong>14 días naturales</strong> posteriores a la recepción del pedido.</li>
                                    <li><strong>No se admitirán devoluciones fuera del plazo indicado.</strong></li>
                                    <li>Para notificaciones sobre roturas en el transporte, las piezas deberán estar en su envoltorio original y ubicadas en la medida de lo posible, de la forma en que fueron enviadas. No se admitirán reclamaciones por rotura en productos que hayan sido manipulados por el cliente.</li>
                                    <li>No se aceptarán reclamaciones o devoluciones de productos ya instalados. El cliente o en su lugar, el instalador, tiene la obligación de verificar el material antes de instalarlo para poder detectar cualquier desperfecto y apartar la pieza afectada.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">COMO HACER UNA DEVOLUCIÓN</div>
                        <div>
                            <p>
                                <ul>
                                    <li>El primer paso para solicitar una devolución de mercancía consistirá en notificarlo al departamento de atención al cliente bien mediante teléfono, whatsapp o email, o al correo electrónico info@ceramicconnection.es</li>
                                    <li>Para una correcta devolución de la mercancía, el palet deberá devolverse perfectamente embalado de forma que las cajas queden bien sujetas, tal y como se recibió</li>
                                    <li>Ceramic Connection no se responsabiliza de los daños causados por un embalaje inadecuado.</li>
                                    <li>Si se trata de un producto defectuoso o error en el envío, los gastos de devolución correrán a cargo de Ceramic Connection.</li>
                                    <li>Si la devolución se debe a una decisión del cliente o un error ocurrido durante el pedido, los gastos correrán a cargo del cliente.</li>
                                    <li>Se recomienda encarecidamente solicitar muestras gratuitas antes de realizar el pedido para evitar posibles errores en la compra.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">REEMBOLSO</div>
                        <div>
                            <p>
                                <ul>
                                    <li>El importe se reembolsará utilizando <strong>el mismo método de pago</strong> con el que se realizó la compra.</li>
                                    <li>En caso de que la devolución se produzca por motivo ajenos a Ceramic Connection, se reembolsará el coste íntegro de la mercancía comprada, una vez se haya revisado su estado, no reembolsando los gastos pagados por los portes dado que el servicio de envío ya se ha prestado.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                {/if}

                {if $language.id==2}
                    <div>
                        <div class="modal-subtitle">DÉLAIS ET CONDITIONS GÉNÉRALES</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Les retours peuvent être demandés dans les <strong>14 jours calendaires</strong> suivant la réception de la commande.</li>
                                    <li><strong>Les retours après ce délai ne seront pas acceptés.</strong></li>
                                    <li>Pour toute notification de casse pendant le transport, les pièces doivent être dans leur emballage d’origine et, dans la mesure du possible, placées comme elles ont été envoyées. Les réclamations pour casse ne seront pas acceptées pour les produits manipulés par le client.</li>
                                    <li>Les réclamations ou retours de produits déjà installés ne seront pas acceptés. Le client ou, le cas échéant, l’installateur, a l’obligation de vérifier le matériel avant l’installation afin de détecter tout défaut et d’écarter la pièce affectée.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">COMMENT EFFECTUER UN RETOUR</div>
                        <div>
                            <p>
                                <ul>
                                    <li>La première étape pour demander un retour consiste à en informer le service client par téléphone, WhatsApp ou e-mail, ou à l’adresse info@ceramicconnection.es</li>
                                    <li>Pour un retour correct de la marchandise, la palette doit être retournée parfaitement emballée, de manière à ce que les boîtes restent bien fixées, comme elle a été reçue.</li>
                                    <li>Ceramic Connection n’est pas responsable des dommages causés par un emballage inadéquat.</li>
                                    <li>En cas de produit défectueux ou d’erreur d’expédition, les frais de retour sont à la charge de Ceramic Connection.</li>
                                    <li>Si le retour est dû à une décision du client ou à une erreur survenue lors de la commande, les frais sont à la charge du client.</li>
                                    <li>Il est fortement recommandé de demander des échantillons gratuits avant de passer commande pour éviter toute erreur d’achat.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">REMBOURSEMENT</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Le montant sera remboursé en utilisant <strong>le même mode de paiement</strong> que celui utilisé pour l’achat.</li>
                                    <li>Si le retour est dû à un motif extérieur à Ceramic Connection, le coût total des marchandises achetées sera remboursé après vérification de leur état, sans remboursement des frais de port déjà payés, car le service de livraison a déjà été fourni.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                {/if}

                {if $language.id==3}
                   <div>
                        <div class="modal-subtitle">GENERAL TERMS AND CONDITIONS</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Returns can be requested within <strong>14 calendar days</strong> of receiving the order.</li>
                                    <li><strong>Returns after this period will not be accepted.</strong></li>
                                    <li>For notifications of breakage during transport, items must be in their original packaging and, as far as possible, placed as they were sent. Claims for breakage will not be accepted for products handled by the customer.</li>
                                    <li>Claims or returns for products already installed will not be accepted. The customer, or the installer on their behalf, is obliged to check the material before installation to detect any defects and set aside the affected piece.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">HOW TO MAKE A RETURN</div>
                        <div>
                            <p>
                                <ul>
                                    <li>The first step to request a return is to notify customer service by phone, WhatsApp, or email, or to info@ceramicconnection.es</li>
                                    <li>For a correct return, the pallet must be returned properly packaged so that the boxes remain securely in place, as received.</li>
                                    <li>Ceramic Connection is not responsible for damage caused by inadequate packaging.</li>
                                    <li>If it is a defective product or shipping error, the return costs will be covered by Ceramic Connection.</li>
                                    <li>If the return is due to a customer decision or an error in the order, the costs will be borne by the customer.</li>
                                    <li>It is strongly recommended to request free samples before placing the order to avoid possible purchase errors.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">REFUND</div>
                        <div>
                            <p>
                                <ul>
                                    <li>The amount will be refunded using <strong>the same payment method</strong> used for the purchase.</li>
                                    <li>If the return is due to reasons unrelated to Ceramic Connection, the full cost of the purchased goods will be refunded after checking their condition, without refunding any shipping fees already paid, as the delivery service has already been provided.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                {/if}

                {if $language.id==4}
                    <div>
                        <div class="modal-subtitle">ALLGEMEINE FRISTEN UND BEDINGUNGEN</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Rücksendungen können innerhalb von <strong>14 Kalendertagen</strong> nach Erhalt der Bestellung beantragt werden.</li>
                                    <li><strong>Rücksendungen nach Ablauf dieser Frist werden nicht akzeptiert.</strong></li>
                                    <li>Für Meldungen von Transportschäden müssen die Teile in ihrer Originalverpackung und nach Möglichkeit so wie beim Versand zurückgelegt werden. Reklamationen für Schäden an vom Kunden bearbeiteten Produkten werden nicht akzeptiert.</li>
                                    <li>Reklamationen oder Rücksendungen bereits installierter Produkte werden nicht akzeptiert. Der Kunde oder ggf. der Installateur ist verpflichtet, das Material vor der Installation zu überprüfen, um Schäden zu erkennen und das betroffene Teil auszuschließen.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">WIE MAN EINE RÜCKSENDUNG VORNEHMT</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Der erste Schritt zur Beantragung einer Rücksendung besteht darin, den Kundenservice per Telefon, WhatsApp oder E-Mail oder an info@ceramicconnection.es zu benachrichtigen.</li>
                                    <li>Für eine korrekte Rücksendung muss die Palette ordnungsgemäß verpackt zurückgegeben werden, sodass die Kartons sicher befestigt bleiben, wie sie empfangen wurden.</li>
                                    <li>Ceramic Connection haftet nicht für Schäden, die durch unzureichende Verpackung verursacht werden.</li>
                                    <li>Bei fehlerhaften Produkten oder Versandfehlern übernimmt Ceramic Connection die Rücksendekosten.</li>
                                    <li>Liegt die Rücksendung an einer Entscheidung des Kunden oder einem Fehler bei der Bestellung, trägt der Kunde die Kosten.</li>
                                    <li>Es wird dringend empfohlen, vor der Bestellung kostenlose Muster anzufordern, um mögliche Fehlkäufe zu vermeiden.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">RÜCKERSTATTUNG</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Der Betrag wird mit <strong>der gleichen Zahlungsmethode</strong> erstattet, die für den Kauf verwendet wurde.</li>
                                    <li>Wenn die Rücksendung aus Gründen erfolgt, die nicht von Ceramic Connection verursacht wurden, wird der volle Kaufpreis der Waren nach Überprüfung des Zustands erstattet, jedoch nicht die bereits gezahlten Versandkosten, da der Versandservice bereits erbracht wurde.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                {/if}

                {if $language.id==5}
                    <div>
                        <div class="modal-subtitle">PRAZOS E CONDIÇÕES GERAIS</div>
                        <div>
                            <p>
                                <ul>
                                    <li>As devoluções podem ser solicitadas dentro de <strong>14 dias naturais</strong> após o recebimento do pedido.</li>
                                    <li><strong>Não serão aceitas devoluções fora do prazo indicado.</strong></li>
                                    <li>Para notificações de avarias durante o transporte, as peças devem estar na embalagem original e, na medida do possível, colocadas da forma como foram enviadas. Não serão aceites reclamações por danos em produtos manuseados pelo cliente.</li>
                                    <li>Não serão aceites reclamações ou devoluções de produtos já instalados. O cliente, ou o instalador em seu lugar, tem a obrigação de verificar o material antes da instalação para detectar qualquer defeito e separar a peça afetada.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">COMO FAZER UMA DEVOLUÇÃO</div>
                        <div>
                            <p>
                                <ul>
                                    <li>O primeiro passo para solicitar uma devolução é notificar o serviço de atendimento ao cliente por telefone, WhatsApp ou e-mail, ou pelo correio info@ceramicconnection.es</li>
                                    <li>Para uma devolução correta, o palete deve ser devolvido perfeitamente embalado, de forma que as caixas permaneçam bem fixas, como foram recebidas.</li>
                                    <li>Ceramic Connection não se responsabiliza por danos causados por embalagens inadequadas.</li>
                                    <li>Se for um produto defeituoso ou erro de envio, os custos da devolução serão suportados pela Ceramic Connection.</li>
                                    <li>Se a devolução se dever a uma decisão do cliente ou a um erro durante o pedido, os custos serão suportados pelo cliente.</li>
                                    <li>Recomenda-se solicitar amostras gratuitas antes de fazer o pedido para evitar possíveis erros na compra.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">REEMBOLSO</div>
                        <div>
                            <p>
                                <ul>
                                    <li>O valor será reembolsado utilizando <strong>o mesmo método de pagamento</strong> usado na compra.</li>
                                    <li>Se a devolução ocorrer por motivos alheios à Ceramic Connection, será reembolsado o custo total da mercadoria adquirida, após verificação do seu estado, sem reembolso dos custos de envio já pagos, uma vez que o serviço de entrega já foi prestado.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                {/if}

                {if $language.id==6}
                    <div>
                        <div class="modal-subtitle">TERMEN EN ALGEMENE VOORWAARDEN</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Retouren kunnen worden aangevraagd binnen <strong>14 kalenderdagen</strong> na ontvangst van de bestelling.</li>
                                    <li><strong>Retouren na deze termijn worden niet geaccepteerd.</strong></li>
                                    <li>Voor meldingen van breuk tijdens transport moeten de artikelen in hun originele verpakking en, indien mogelijk, geplaatst zoals verzonden worden. Claims voor breuk aan door de klant gehanteerde producten worden niet geaccepteerd.</li>
                                    <li>Claims of retouren van reeds geïnstalleerde producten worden niet geaccepteerd. De klant of, indien van toepassing, de installateur is verplicht het materiaal te controleren voordat het wordt geïnstalleerd om eventuele defecten te detecteren en het betreffende onderdeel apart te houden.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">HOE EEN RETOUR TE MAAKEN</div>
                        <div>
                            <p>
                                <ul>
                                    <li>De eerste stap om een retour aan te vragen is het informeren van de klantenservice via telefoon, WhatsApp of e-mail, of naar info@ceramicconnection.es</li>
                                    <li>Voor een correcte retour moet het pallet correct verpakt worden teruggestuurd, zodat de dozen goed op hun plaats blijven zoals ontvangen.</li>
                                    <li>Ceramic Connection is niet verantwoordelijk voor schade veroorzaakt door inadequate verpakking.</li>
                                    <li>Bij een defect product of verzendfout worden de retourkosten door Ceramic Connection gedragen.</li>
                                    <li>Als de retour het gevolg is van een beslissing van de klant of een fout bij de bestelling, zijn de kosten voor rekening van de klant.</li>
                                    <li>Het wordt sterk aanbevolen gratis monsters aan te vragen voordat u bestelt om mogelijke aankoopfouten te voorkomen.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <div class="modal-subtitle">TERUGBETALING</div>
                        <div>
                            <p>
                                <ul>
                                    <li>Het bedrag wordt terugbetaald via <strong>dezelfde betaalmethode</strong> die voor de aankoop is gebruikt.</li>
                                    <li>Als de retour het gevolg is van redenen buiten Ceramic Connection, wordt de volledige kosten van de gekochte goederen terugbetaald na controle van de staat, zonder terugbetaling van reeds betaalde verzendkosten, aangezien de verzendservice al is geleverd.</li>
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