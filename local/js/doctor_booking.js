let DoctorBooking = BX.namespace('DoctorBooking');

DoctorBooking.helloWorld = function( text ) {
 let tt = BX.message('DOCTOR_BOOKING_HELLO_WORLD')+ " " + text;
  alert(tt);
};


DoctorBooking.showMessage = function (message) {
        alert(message);
    };

DoctorBooking.addBookingRecord = function(doc_Id, proc_Id, name_, date_time ) {
        BX.ajax.runComponentAction('anb:booking', 'addBookingRecord', {
            mode: 'ajax',
            data: {
                docId: doc_Id,
                procId: proc_Id,
                name: name_,
                datetime: date_time,
            },
        }).then(response => {
            DoctorBooking.showMessage('Вы успешно записаны' );//response.data.BOOKING_ID);
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }
            DoctorBooking.showMessage(errorMessage);
        });
}


var popupFields =  
// BX.create( 
{
    tag: 'div', 
    props: {
            width: '100%',
            id: 'doctorBookingForm',                
    },

    children: [ 
        BX.message( 'DOCTOR_BOOKING_POPUP_SUBTITLE' ),
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
                                attrs: { id: 'patientDate', type: 'datetime-local', name: 'patientDate' },
                                events: {
                                    change: function (){
                                        //Определяем текущее время
                                        currentTime = new Date();
                                        if  ( new Date(this.value) < currentTime ) {
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
                                        //Определяем текущее время
                                        currentTime = new Date().value;
                                        if  ( new Date(this.value) < currentTime ) {
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
            ]
        }),
    ]
} ;
// );




// вызов модального окна
DoctorBooking.showPopup = function( doctorName, docId, procName, procId ) {
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
        content : BX.create( {
            tag: 'div', 
            children: [ 
                BX.create( "div", { html: '<div>Для записи к доктору: <b>'+doctorName+'</b></br>на процедуру: <b>' + procName + '</b></br></br></div>'}),
                BX.create( "div", popupFields)
            ]
        } ),

        buttons : [ //Создаем кнопки на окне
            new BX.PopupWindowButton( { //Кнопка добавления
                text : BX.message( 'DOCTOR_BOOKING_POPUP_BUTTON_ACC' ), //Надпись на кнопке
                className : 'popup-window-button-accept', //Класс стиля кнокпи
                events : { //Событие по кнопке
                    click : function () {
                        BX.fireEvent(BX('patientName'),'change');
						BX.fireEvent(BX('patientDate'),'change');

                        const getValName = document.getElementById("patientName"); 
                        const getValDate = document.getElementById("patientDate"); 

                        const currentTime = new Date(); 
                        const valDate = new Date( getValDate.value ) 
                        if ( ( getValName.value.length<=3 ) || ( !/[а-яА-ЯЁё]/.test(getValName.value) ) || 
                             ( valDate < currentTime ) ) { 
                            return false;
                        }
                        else {
                                
                            // var request = BX.ajax.runAction('anb:firstmodule.controller.test.example', {
                            //     data: {
                            //         param1: 'hhh'
                            //     }
                            // });
                            
                            // request.then(function(response){
                            //     alert(response);
                            //     console.log(response);
                            // });

                            DoctorBooking.addBookingRecord(docId, procId, getValName.value, getValDate.value );
                            // DoctorBooking.helloWorld(getValName.value); //Вызов метода
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
    
    //Сбрасываем имя
     const getValName = document.getElementById("patientName"); 
     getValName.value = "";   
    //Определяем текущее время
    const currentTime = new Date();
    
    //Костыль для перевода в текущую локаль.
    //Как сделать правильно?
    currentTime.setHours( currentTime.getHours() + 5 );
    document.getElementById('patientDate').value = currentTime.toISOString().slice(0, 16);

    // выводим окно
    DoctorBooking.popup.show();
};