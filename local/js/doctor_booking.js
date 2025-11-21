let DoctorBooking = BX.namespace('DoctorBooking');

DoctorBooking.helloWorld = function( text ) {
 let tt = BX.message('DOCTOR_BOOKING_HELLO_WORLD')+ " " + text;
  alert(tt);
};

var popupFields =  BX.create( {
    tag: 'div', 
    props: {
            width: '100%',
            id: 'doctorBookingForm',                
    },
    children: [ 
        BX.create("table", {
            props: {
                width: '100%',
            },            
            children: [ 
                BX.create("tr", {
                    props: {
                        width: '100%',
                    },
                    children: [ 
                        BX.create("td", {
                            props: {
                                width: '30%',
                            },
                            tag: 'label', 
                            attrs: { for: 'patientName' }, 
                            text: 'Ваше имя' 
                        } ),
                    BX.create(  {
                        props: {
			                className: 'doctor-booking-add-form',
                        },
                        children: [ 
                            BX.create( {
                                tag: 'input', 
                                attrs: { id: 'patientName', type: 'text', placeholder: 'Ваше имя', name: 'patientName' }, 
                                events: {
                                    change: function (){
                                        if(
                                            this.value.length<=3 || 
                                            !/[а-яА-ЯЁё]/.test(this.value)
                                        ){
                                            BX.adjust(BX(this),{
                                                props:{className: "form-control invalid"}
                                            });
                                            return false;
                                        } else {
                                            BX.adjust(BX(this),{
                                                props:{className: "form-control"}
                                            });
                                            return true;
                                        }
                                    },
                                    input: function (){
                                        if(this.value.length<=3 || 
                                            !/[а-яА-ЯЁё]/.test(this.value)
                                        ){
                                            BX.adjust(BX(this),{
                                                props:{className: "form-control invalid"}
                                            });
                                            return false;
                                        } else {
                                            BX.adjust(BX(this),{
                                                props:{className: "form-control"}
                                            });
                                            return true;
                                        }
                                    }
                                }
                            } ),
                        ]
                    } ),    
                    ]
                } ),
                
                BX.create("tr", {
                    props: {
                        width: '100%',
                    },
                    children: [ 
                        BX.create("td", {
                            props: {
                                width: '30%',
                            },
                            tag: 'label', 
                            attrs: { for: 'patientDate' }, 
                            text: 'Дата и время записи' 
                        } ),
                    BX.create(  {
                        props: {
			                className: 'doctor-booking-add-form',
                        },
                        children: [ 
                            BX.create( {
                                tag: 'input', 
                                attrs: { id: 'patientDate', type: 'datetime-local', name: 'patientDate' } 
                            } ),
                        ]
                    } ),    
                    ]
                } ),        
            ]
        }),
    ]
} );




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
        content : popupFields, //BX.create('div', {
        //     html : '<p>' + BX.message( 'DOCTOR_BOOKING_POPUP_SUBTITLE' ) + '</p><textarea>' + item + '</textarea>' 
        // } ),

        buttons : [ //Создаем кнопки на окне
            new BX.PopupWindowButton( { //Кнопка добавления
                text : BX.message( 'DOCTOR_BOOKING_POPUP_BUTTON_ACC' ), //Надпись на кнопке
                className : 'popup-window-button-accept', //Класс стиля кнокпи
                events : { //Событие по кнопке
                    click : function () {
                        BX.fireEvent(BX('patientName'),'change');
						// BX.fireEvent(BX('reviewText'),'change');

                        const getVal = document.getElementById("patientName");   
                        if ( ( getVal.value.length<=3 ) || !/[а-яА-ЯЁё]/.test(getVal.value) ) { 
                            return false;
                        }
                        else {
                            DoctorBooking.helloWorld(getVal.value); //Вызов метода
                            this.popupWindow.close();
                        }
                        
                    }
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