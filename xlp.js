function test(form){
    if (form.energy.value == "") {
        alert ("Не заполнено поле");
        return false;
    }
  return true;
}