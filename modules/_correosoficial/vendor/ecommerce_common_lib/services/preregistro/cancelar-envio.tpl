{**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 *}
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.correos.es/iris6/services/preregistroetiquetas">
    <soapenv:Header ></soapenv:Header>
    <soapenv:Body>
        <PeticionAnular> 
        <IdiomaErrores>{$shipping_data.idioma|escape:'html':'UTF-8'}</IdiomaErrores>
        <codCertificado>{$shipping_data.codCertificado|escape:'html':'UTF-8'}</codCertificado>
        </PeticionAnular>
    </soapenv:Body>
</soapenv:Envelope>