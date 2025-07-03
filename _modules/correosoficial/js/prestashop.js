// Conseguimos la url base
const getUrl = window.location;
const platform = 'ps';

// Prefijo url de administración.
let url_prefix_admin;

// Despachador
const dispatcher_url = window.location.href.split('?')[0];

// General
const AdminCorreosOficialActiveCustomers = dispatcher_url + '?controller=AdminCorreosOficialActiveCustomers';

// Inicio
const AdminHomeSendMail = dispatcher_url + '?controller=AdminHomeSendMail';

// Datos de cliente
const AdminCorreosOficialCustomerDataProcess = dispatcher_url + '?controller=AdminCorreosOficialCustomerDataProcess';

// DataTable de cliente
const getDataTableCustomerList =
    dispatcher_url + '?controller=AdminCorreosOficialCustomerDataProcess&action=getDataTableCustomerList';

const getCustomerCode =
    dispatcher_url + '?controller=AdminCorreosOficialCustomerDataProcess&action=getCustomerCode';

const getCustomerCodes =
    dispatcher_url + '?controller=AdminCorreosOficialCustomerDataProcess&action=getCustomerCodes';

// Servicios de Correos
const AdminCorreosSoapRequestURL = dispatcher_url + '?controller=AdminCorreosSOAPRequest';

// Servicios de CEX
const AdminCEXRestRequestURL = dispatcher_url + '?controller=AdminCEXRestRequest';

// Ajustes
const AdminCorreosOficialSettingsGetDataTable =
    dispatcher_url + '?controller=AdminCorreosOficialSettings&action=getDataTable';

// Ajustes->Remitentes
const AdminCorreosOficialSendersProcess = dispatcher_url + '?controller=AdminCorreosOficialSendersProcess';

// Ajustes->Configuracion de usuario
const AdminCorreosOficialUserConfigurationProcess =
    dispatcher_url + '?controller=AdminCorreosOficialUserConfigurationProcess';

// Ajustes->Productos
const AdminCorreosOficialProductsProcess = dispatcher_url + '?controller=AdminCorreosOficialProductsProcess';

// Ajustes->Zonas y transportistas
const AdminCorreosOficialZonesCarriersProcess = dispatcher_url + '?controller=AdminCorreosOficialZonesCarriersProcess';

// Ajustes->Tramitación Aduanera
const AdminCorreosOficialCustomsProcessingProcess =
    dispatcher_url + '?controller=AdminCorreosOficialCustomsProcessingProcess';

// Cron
const AdminCorreoOficiaCronURL = dispatcher_url + '?controller=AdminCorreosOficialCronProcess&operation=cronExecute';
const dispatcher_cron_url = window.location.pathname.split('/sell')[0];
const AdminCorreosOficialExecuteCron =
    dispatcher_cron_url + '?controller=AdminCorreosOficialCronProcess&operation=EXECUTECRON';

// Servicios de Correos
const AdminCorreosSoapRequestURLConsole = dispatcher_url + '?controller=AdminCorreosSOAPRequestConsole';
const AdminCorreosRestRequestURLConsole = dispatcher_url + '?controller=AdminCorreosRestRequestConsole';
// Servicios de CEX
const AdminCEXRestRequestURLConsole = dispatcher_url + '?controller=AdminCEXRestRequestConsole';

const pathname = window.location.pathname.split('/')[1];
const url_prefix = '/' + pathname;
const url_prefix_back = window.location.href.split('?')[0];

/** Base_Uri Prestashop */
const co_ps_base_uri = jQuery('#co_ps_base_uri').val();

/*  
    20/12/2023
	Parche correctivo para INC000052549086 Prestashop - No aparece oficina para elegir
    El módulo ThemeVolty - Newsletter Popup sobrescribe la variable de javascript baseDir, que nosotros leemos. 
	Esta variable de de Prestashop, y no debería sobreescribirse
	La informamos a "/" que es lo que nos hace falta.
 */
if (!getUrl.host.includes('correos') && getUrl.host !='localhost') { 
	if ((typeof baseDir !== 'undefined' && baseDir.substring('http'))){
		baseDir = '/';
	}
}

// Para admin
if (typeof baseDir !== 'undefined') {
    url_prefix_admin = getUrl.protocol + '//' + getUrl.host + baseDir;
  // Para front
} else {
    url_prefix_admin = document.URL.substring(0, document.URL.lastIndexOf('/')) + '/';
}

// Ruta hacia el módulo. Se usa para la descarga de etiquetas.
const co_path_to_module = url_prefix_admin + 'modules/correosoficial';

// Servicos de Oficinas/CityPaq
const FrontCheckoutAdminURL =
    url_prefix_admin + '/index.php?fc=module&module=correosoficial&controller=checkout&id_lang=1&';
const AdminOrderURL = dispatcher_url + '/index.php?controller=AdminCorreosOficialOrder&';
