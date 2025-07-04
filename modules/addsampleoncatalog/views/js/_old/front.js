
$( document ).ready( function () {

    $('.product-table-cell').on('click', function() {
        var productId = $(this).data('product-id');
        var cartId = $(this).data('id-cart');
    
        $.ajax({
            type: 'POST',
            url: $(this).data('ajax-url'),
            data: {
                action: 'addToCart',
                id_product: productId,
                id_cart: cartId
            },
            success: function(response) {
             
                if (response.success) {
                    console.log('Producto agregado al carrito con éxito');
                } else {
                    console.error('Error al agregar el producto al carrito:', response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
            }
        });
    }); 

    //var shareButtons = document.querySelectorAll('[data-share-button]');
    //var shareMenus = document.querySelectorAll('[data-share-menu]');
    //var copyButtons = document.querySelectorAll('[data-share-id]');

    document.querySelectorAll('[data-share-button]').forEach(function(button, index) {  
        button.addEventListener('click', function(event) {
      
            event.stopPropagation();
            document.querySelectorAll('[data-share-menu]').forEach(function(menu, menuIndex) {
                if (index === menuIndex) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                } else {
                    menu.style.display = 'none';
                }
            });
        });
    });

    document.addEventListener('click', function(event) {
        document.querySelectorAll('[data-share-menu]').forEach(function(menu) {
            if (!menu.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    });

    function copyToClipboard(text) {
        var textField = document.createElement('textarea');
        textField.innerText = text;
        document.body.appendChild(textField);
        textField.select();
        document.execCommand('copy');
        textField.remove();
        alert('Enlace copiado al portapapeles');
    }
    
    document.querySelectorAll('[data-share-id]').forEach(function(button) {
        button.addEventListener('click', function(event) {
            var linkElement = button.getAttribute('data-share-id');
            copyToClipboard(linkElement);
        });
    });

    function copyToClipboard(text) {
        var decodedText = decodeURIComponent(text);
        var textField = document.createElement('textarea');
        textField.innerText = decodedText;
        document.body.appendChild(textField);
        textField.select();
        document.execCommand('copy');
        textField.remove();
        alert('Enlace copiado al portapapeles');
    }
    
    function convertSlugToTitle(slug) {
        // Eliminar la extensión .html
        let stringWithoutExtension = slug.replace('.html', '');
        
        // Reemplazar guiones por espacios
        let stringWithSpaces = stringWithoutExtension.replace(/-/g, ' ');
        
        // Capitalizar cada palabra
        let capitalizedString = stringWithSpaces.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
        
        return capitalizedString;
    }

    function getColorPath(url, color, lang) {

        let originalColorName = color;
        // Reemplazar caracteres con acentos por su equivalente sin acento
        color = color.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        
        color = color.toLowerCase();

        color = color.replace(/\s+/g, '-');

        let path = {'url' : url};
        if(lang == 'es') {
            path.url += '/azulejos/color/' + color;
            path.name = 'Azulejos Color ' + originalColorName;
        }
        
        if(lang == 'fr') {
            path.url += '/carrelage/couleur/' + color;
            path.name = 'Carrelage Couleur ' + originalColorName;
        }
        
        if(lang == 'en') {
            path.url += '/tiles/color/' + color;
            path.name = originalColorName + ' Tiles';
        }
        
        if(lang == 'de') {
            path.url += '/fliesen/farbe/' + color;
            path.name = originalColorName + ' Fliesen';
        }
        
        if(lang == 'pt') {
            path.url += '/azulejos/cor/' + color;
            path.name = 'Azulejos Cor ' + originalColorName;
        }
        
        if(lang == 'nl') {
            path.url += '/tegels/kleur/' + color;
            path.name = originalColorName + ' Tegels';
        }
        
        return path;
    }

    if (document.getElementById('bread-crumps-container')) {
        const currentUrl = window.location.href;

        // Parsear la URL para obtener las partes necesarias
        const url = new URL(currentUrl);
        const pathSegments = url.pathname.split('/').filter(segment => segment.length > 0);

        // Base URL (ceramic/lang)
        const baseUrl = url.origin + '/' + pathSegments[0] ;

        let homeText = 'azulejos';
        let tileText = "Azulejos";
        let bold = 'style="font-weight:bolder; color:black"';
                
        // si color != '' o color != 'none' breadcrump de producto, sino, de categoría
        let color = document.getElementById('bread-crumps-container').dataset.color;
        let location = document.getElementById('bread-crumps-container').dataset.location;

        let breadCrumpsHtml = '';
        
        //textos base
        if (pathSegments[0] == 'fr') {
            homeText = 'carrelage';
            tileText = 'Carrelage';
        }

        if (pathSegments[0] == 'en') {
            homeText = 'tiles';
            tileText = 'Tiles';
        }

        if (pathSegments[0] == 'pt') {
            homeText = 'azulejos';
            tileText = 'Azulejos';
        }
        if (pathSegments[0] == 'de') {
            tileText = 'Fliesen';
            homeText = 'fliesen';
        }

        if (pathSegments[0] == 'nl') {
            tileText = 'Tegels';
            homeText = 'tegels';
        }

        let currentPath = baseUrl;

        pathSegments.forEach((segment, index) => {
            
            
            if(index != 0 ){

                if (index != 1) {
                    bold = '';
                }
                // Actualizar la ruta actual
                currentPath += '/' + segment ;

                if (index === pathSegments.length - 1) {
                    // Último segmento, solo texto
                    if(document.getElementById('aspecto-link')) {
                        let aspectoLinkDiv = document.getElementById('aspecto-link');
                        let linkElement = aspectoLinkDiv.querySelector('a');
                        let linkHref = linkElement.getAttribute('href');
                        let linkText = linkElement.innerHTML;

                        breadCrumpsHtml += '<a href="' + linkHref + '" data-index="'+ index +'" '+ bold +'>' + tileText + ' ' + linkText + '</a> > ';

                    }else{

                        if (color != 'none') {
                            path = getColorPath(baseUrl, color, pathSegments[0]);
                            breadCrumpsHtml += '<a href="' + path.url +'" data-index="'+ index +'" '+ bold +'>' + path.name + '</a> > ';
                        }
                        
                    }

                    breadCrumpsHtml += convertSlugToTitle(segment);

                } else {
                    // Segmetos intermedios, añadir enlace
                    if (location == 'category') {

                        if ((color == 'none' || color == '') && index != 2) {      
                            breadCrumpsHtml += ' <a href="' + currentPath +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + '</a> > ';  
                        }

                    }else{
                        
                        //link de la categoría 
                        let categoryLinkDiv = document.getElementById('category-link');
                        let categoryQuerySelector = categoryLinkDiv.querySelector('a');
                        let categoryLink = categoryQuerySelector.getAttribute('href');
                        
                        if(document.getElementById('aspecto-link') && index == 1) {
                            //agrego 's' antes del cierre de la etiqueta para hacer el plural
                            breadCrumpsHtml += ' <a href="' + categoryLink +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + 's</a> > ';
                        }
                        else if(!document.getElementById('aspecto-link') && index == 1) {
                            breadCrumpsHtml += ' <a href="' + categoryLink +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + '</a> > ';
                        } else{
                            breadCrumpsHtml += ' <a href="' + currentPath +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + '</a> > ';
                        }
                        
                    }

                }
            }
        });

        // Insertar las migas de pan en el div
        document.getElementById('bread-crumps-container').innerHTML = breadCrumpsHtml;
    }

    if(document.getElementById('openModal')) {

        document.getElementById('openModal').addEventListener('click', function() {
            var videoContainer = document.getElementById('videoContainer');
            
            // Asegúrate de que el video no se inserte más de una vez
            if (videoContainer.innerHTML.trim() === '') {
                videoContainer.innerHTML = `
                    <video autoplay loop muted width="100%" src="/themes/child_classic/assets/video/waste-animation.mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                `;
            }
        });
    
    }

    if(document.getElementById('productVideo')) {

        // Selecciona el elemento de video
        var videoElement = document.getElementById('productVideo');
        var videoElementMobile = document.getElementById('productVideoMobile');

        var videoProductRoute = videoElement.getAttribute('data-url');
    
        // Retrasa la asignación del src del video en 3 segundos
        setTimeout(function() {
            videoElement.src = videoProductRoute;
            videoElementMobile.src = videoProductRoute;
    
            // Intenta iniciar la reproducción
            videoElement.play().then(() => {
                console.log('video is running...');
            }).catch(error => {
                console.warn('Error with video product:', error);
            });

            // Intenta iniciar la reproducción del movil
            videoElementMobile.play().then(() => {
                console.log('video is running...');
            }).catch(error => {
                console.warn('Error with video product:', error);
            });

        }, 1500); 
    }

    //Pasarela de pago
    const checkbox = document.getElementById("conditions_to_approve[terms-and-conditions]");
    const redsysWarning = document.getElementById("redsys-warning-checks");
    const insiteFormContainer = document.getElementById("insite-form-container");

    // Función para manejar el cambio de visibilidad
    function toggleVisibilityRedsysBox() {
        if (checkbox.checked) {
            insiteFormContainer.style.display = "block";
            redsysWarning.style.display = "none";
        } else {
            insiteFormContainer.style.display = "none";
            redsysWarning.style.display = "block";
        }
    }

    // Inicializa el estado correcto al cargar la página
    if (checkbox) {
        
        // Escucha el cambio en el checkbox
        checkbox.addEventListener("change", toggleVisibilityRedsysBox);

        toggleVisibilityRedsysBox();
    }
    
    //LOADING LAZY IMAGENES MENU
    const openMenuButton = document.querySelector('#openMenuButton'); // El botón que abre el menú
    const menuContainer = document.querySelector('#menu-ceramic'); // El contenedor del submenú
  
    const loadMenuImages = () => {
      const allMenuImages = menuContainer.querySelectorAll('img[data-src]'); 
      allMenuImages.forEach(img => {
        img.src = img.dataset.src; 
        img.removeAttribute('data-src');
      });
  
      // Cargar también las imágenes con la clase .menu-image
      const menuImageElements = document.querySelectorAll('.menu-image[data-src]'); // Buscar imágenes con la clase .menu-image
      menuImageElements.forEach(img => {
        img.src = img.dataset.src; 
        img.removeAttribute('data-src'); 
      });
    };
  
    // Detectar clic en el botón de apertura del menú
    openMenuButton.addEventListener('click', () => {
      loadMenuImages(); // Cargar las imágenes cuando se hace clic en el botón del menú
    });
  
    // Configuración del MutationObserver para detectar cuando el contenedor #menu-ceramic adquiere la clase `bg_submenu`
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        // Comprobar si el div #menu-ceramic ahora tiene la clase bg_submenu
        if (mutation.target.classList.contains('bg_submenu')) {
          loadMenuImages(); // Cargar todas las imágenes cuando el menú se haga visible
          observer.disconnect(); // Detener el observador después de cargar las imágenes
        }
      });
    });
  
    // Observar cambios en las clases del div #menu-ceramic
    observer.observe(menuContainer, { attributes: true, attributeFilter: ['class'] });
  
    // LOADING LAZY IMAGENES NORMALES
    const lazyLoadRegularImages = () => {
      const lazyImages = document.querySelectorAll('img[data-src]:not(.menu-image)'); // Excluye imágenes del menú
      const ioObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            observer.unobserve(img);
          }
        });
      });
  
      lazyImages.forEach(img => ioObserver.observe(img));
    };
  
    // Inicializa el IntersectionObserver para las imágenes fuera del menú
    lazyLoadRegularImages();
      
});

// Escuchar el evento 'updateFacets' de Prestashop para actualizar la meta tag de robots
prestashop.on('updateFacets', function() {
    let metaTag = document.querySelector('meta[name="robots"]');
    metaTag.setAttribute('content', 'noindex, nofollow');
});
    


function validarDNI() {
    const dniInput = document.getElementById("field-dni").value.toUpperCase();
    const dniRegex = /^[0-9]{8}[A-Z]$/;  // DNI: 8 dígitos y 1 letra
    const nieRegex = /^[XYZ][0-9]{7}[A-Z]$/;  // NIE: X, Y o Z seguido de 7 dígitos y 1 letra
    const cifRegex = /^[ABCDEFGHJKLMNPQRSVW][0-9]{7}[0-9A-J]$/; // CIF

    if (dniRegex.test(dniInput)) {
        // Validación del DNI
        const numero = parseInt(dniInput.slice(0, 8));
        const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        const letraEsperada = letras[numero % 23];
        return dniInput[8] === letraEsperada;
    } else if (nieRegex.test(dniInput)) {
        // Validación del NIE
        let numero = dniInput.slice(1, 8);
        switch (dniInput[0]) {
            case 'X': numero = '0' + numero; break;
            case 'Y': numero = '1' + numero; break;
            case 'Z': numero = '2' + numero; break;
        }
        const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        const letraEsperada = letras[parseInt(numero) % 23];
        return dniInput[8] === letraEsperada;
    } else if (cifRegex.test(dniInput)) {
        // Validación del CIF
        return isValidCif(dniInput);
    }

    return false; // Si no coincide con ninguna de las expresiones regulares
}

// Función para validar el CIF
function isValidCif(cif) {
    if (!cif || cif.length !== 9) {
        return false;
    }

    var letters = ['J', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
    var digits = cif.substr(1, cif.length - 2);
    var letter = cif.substr(0, 1);
    var control = cif.substr(cif.length - 1);
    var sum = 0;

    if (!letter.match(/[A-Z]/)) {
        return false;
    }

    for (let i = 0; i < digits.length; ++i) {
        let digit = parseInt(digits[i]);

        if (isNaN(digit)) {
            return false;
        }

        if (i % 2 === 0) {
            digit *= 2;
            if (digit > 9) {
                digit = Math.floor(digit / 10) + (digit % 10);
            }
        }

        sum += digit;
    }

    sum %= 10;
    let digitControl = (sum !== 0) ? (10 - sum) : 0;

    if (letter.match(/[ABEH]/)) {
        return String(digitControl) === control;
    }
    if (letter.match(/[NPQRSW]/)) {
        return letters[digitControl] === control;
    }

    return String(digitControl) === control || letters[digitControl] === control;
}

// Escucha el evento submit
if(document.getElementById("address-form")) {
    document.getElementById("address-form").addEventListener("submit", function(event) {
        console.log($('#field-id_country').val());
        if ($('#field-id_country').val() == 6 && !validarDNI()) {
            event.preventDefault();
            const errorSpan = document.getElementById("dni-error");
            errorSpan.style.display = "block";
            errorSpan.innerText = "Formato incorrecto. Introduzca un DNI, NIE o CIF válido.";
            setTimeout(() => {
                document.getElementById("confirmAddressButton").classList.remove("disabled");
                document.getElementById("confirmAddressButton").disabled = false;
            }, 100);
        } else {
            document.getElementById("dni-error").style.display = "none";
            this.submit();
        }
    });
    

}

prestashop.on( 'updatedAddressForm', function(){

    function validarDNI() {
        const dniInput = document.getElementById("field-dni").value.toUpperCase();
        const dniRegex = /^[0-9]{8}[A-Z]$/;  // DNI: 8 dígitos y 1 letra
        const nieRegex = /^[XYZ][0-9]{7}[A-Z]$/;  // NIE: X, Y o Z seguido de 7 dígitos y 1 letra
        const cifRegex = /^[ABCDEFGHJKLMNPQRSVW][0-9]{7}[0-9A-J]$/; // CIF

        if (dniRegex.test(dniInput)) {
            // Validación del DNI
            const numero = parseInt(dniInput.slice(0, 8));
            const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
            const letraEsperada = letras[numero % 23];
            return dniInput[8] === letraEsperada;
        } else if (nieRegex.test(dniInput)) {
            // Validación del NIE
            let numero = dniInput.slice(1, 8);
            switch (dniInput[0]) {
                case 'X': numero = '0' + numero; break;
                case 'Y': numero = '1' + numero; break;
                case 'Z': numero = '2' + numero; break;
            }
            const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
            const letraEsperada = letras[parseInt(numero) % 23];
            return dniInput[8] === letraEsperada;
        } else if (cifRegex.test(dniInput)) {
            // Validación del CIF
            return isValidCif(dniInput);
        }

        return false; // Si no coincide con ninguna de las expresiones regulares
    }

    // Función para validar el CIF
    function isValidCif(cif) {
        if (!cif || cif.length !== 9) {
            return false;
        }

        var letters = ['J', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        var digits = cif.substr(1, cif.length - 2);
        var letter = cif.substr(0, 1);
        var control = cif.substr(cif.length - 1);
        var sum = 0;

        if (!letter.match(/[A-Z]/)) {
            return false;
        }

        for (let i = 0; i < digits.length; ++i) {
            let digit = parseInt(digits[i]);

            if (isNaN(digit)) {
                return false;
            }

            if (i % 2 === 0) {
                digit *= 2;
                if (digit > 9) {
                    digit = Math.floor(digit / 10) + (digit % 10);
                }
            }

            sum += digit;
        }

        sum %= 10;
        let digitControl = (sum !== 0) ? (10 - sum) : 0;

        if (letter.match(/[ABEH]/)) {
            return String(digitControl) === control;
        }
        if (letter.match(/[NPQRSW]/)) {
            return letters[digitControl] === control;
        }

        return String(digitControl) === control || letters[digitControl] === control;
    }

    // Escucha el evento submit
    if(document.getElementById("address-form")) {
        document.getElementById("address-form").addEventListener("submit", function(event) {
            console.log($('#field-id_country').val());
            if ($('#field-id_country').val() == 6 && !validarDNI()) {
                event.preventDefault();
                const errorSpan = document.getElementById("dni-error");
                errorSpan.style.display = "block";
                errorSpan.innerText = "Formato incorrecto. Introduzca un DNI, NIE o CIF válido.";
                setTimeout(() => {
                    document.getElementById("confirmAddressButton").classList.remove("disabled");
                    document.getElementById("confirmAddressButton").disabled = false;
                }, 100);
            } else {
                document.getElementById("dni-error").style.display = "none";
                this.submit();
            }
        });
        

    }

 });

