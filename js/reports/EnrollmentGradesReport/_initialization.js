/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$("#print").on('click', function() {
    window.print();
});

function limpar() {
    $("#report, #print").hide();
}
