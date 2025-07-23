document.addEventListener('DOMContentLoaded', function() {
     // Seleccionamos el primer input con name="updatecatalogFilter_id"
     var filterIdElement = document.getElementsByName('updatecatalogFilter_id')[0];
    
     if (filterIdElement) {
         // Encontramos el primer <tr> que está por encima del elemento
         var trElement = filterIdElement.closest('tr'); // 'closest' encuentra el primer tr más cercano
         
         // Si encontramos el <tr>, le aplicamos display: none
         if (trElement) {
             trElement.style.display = 'none';
         }
     }
    //document.getElementsByName('updatecatalogFilter_id')[0].classList.add('center');
    //document.getElementsByName('updatecatalogFilter_position')[0].classList.add('center');

});