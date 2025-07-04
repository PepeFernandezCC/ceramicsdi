document.addEventListener('DOMContentLoaded', function() {
     // Seleccionamos el primer input con filtro
     var filterIdElement = document.getElementsByName('viewproductsbyorderFilter_id_product')[0];
    
     if (filterIdElement) {
         // Encontramos el primer <tr> que está por encima del elemento
         var trElement = filterIdElement.closest('tr'); // 'closest' encuentra el primer tr más cercano
         
         // Si encontramos el <tr>, le aplicamos display: none
         if (trElement) {
             trElement.style.display = 'none';
         }
     }

});