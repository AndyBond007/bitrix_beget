let DoctorBooking = BX.namespace('DoctorBooking');

DoctorBooking.helloWorld = function() {
  alert(BX.message('DOCTOR_BOOKING_HELLO_WORLD'));
};

// вызов модального окна
DoctorBooking.showPopup = function( item ) {
    // если окно уже существует, закрываем и убиваем окно
    if ( DoctorBooking.popup ) {
        DoctorBooking.popup.close();
        DoctorBooking.popup.destroy();
    }
    // генерируем окно по параметрам
    DoctorBooking.popup = new BX.PopupWindow( 'doctor-booking-popup', null, {
        width : 600, //Ширина окна
        height : 400, //Высота окна
        closeByEsc : true, //Закрытие по кнопке Esc
        closeIcon : true, //Закрывающая иконка
        overlay : { //Перекрытие основного окна
            opacity : 50, //Прозрачность
            backgroundColor : '#000000ff' //Цвет перекрытия
        },
        titleBar : BX.message( 'DOCTOR_BOOKING_POPUP_TITLE' ),
        content : BX.create('div', {
            html : '<p>' + BX.message( 'DOCTOR_BOOKING_POPUP_SUBTITLE' ) + '</p><textarea>' + item + '</textarea>'
        }),
        buttons : [ //Создаем кнопки на окне
            new BX.PopupWindowButton( { //Кнопка добавления
                text : BX.message( 'DOCTOR_BOOKING_POPUP_BUTTON_ACC' ), //Надпись на кнопке
                className : 'popup-window-button-accept', //Класс стиля кнокпи
                events : { //Событие по кнопке
                    click : DoctorBooking.helloWorld //Вызов метода
                }
            } ) ,
            new BX.PopupWindowButton( { //Кнопка закрытия окна
                    text : BX.message( 'DOCTOR_BOOKING_POPUP_BUTTON_REJ' ), //Надпись на кнопке
                    className : 'popup-window-button-reject',//Класс стиля кнокпи
                    events : {//Событие по кнопке
                        click: function() {
                            this.popupWindow.close(); // закрытие окна
                        }
                    }
            } ) 
        ]
    } );
    // выводим окно
    DoctorBooking.popup.show();
};