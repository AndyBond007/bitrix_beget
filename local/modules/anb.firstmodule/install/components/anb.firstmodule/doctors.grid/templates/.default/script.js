BX.namespace('Anb.DoctorsGrid');

BX.Anb.DoctorsGrid = {
    signedParams: null,
    init: function(data) {
        this.signedParams = data.signedParams;
    },
    showMessage: function (message) {
        alert(message);
    },
    deleteDoctor(id) {
        BX.ajax.runComponentAction('anb:doctors.grid', 'deleteElement', {
            mode: 'class',
            signedParameters: BX.Anb.DoctorsGrid.signedParams,
            data: {
                doctorId: id,
            },
        }).then(response => {
            BX.Anb.DoctorsGrid.showMessage('Удалена запись по доктору с ID=' + id);

            // var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
            // var gridObject = BX.Main.gridManager.getById('DOCTORS_GRID');  // Идентификатор грида

            // if (gridObject.hasOwnProperty('instance')){
            //     gridObject.instance.reloadTable('POST', reloadParams);
            // }

            let grid = BX.Main.gridManager.getById('DOCTORS_GRID')?.instance;
            if (grid) {
                grid.reload();
            }
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }

            BX.Anb.DoctorsGrid.showMessage(errorMessage);
        });
    },
    deleteDoctorViaAjax(id) {
        BX.ajax.runComponentAction('anb:doctors.grid', 'deleteElement', {
            mode: 'ajax',
            data: {
                doctorId: id,
            },
        }).then(response => {
            BX.Anb.DoctorsGrid.showMessage('Удалена книга с ID=' + id);
            let grid = BX.Main.gridManager.getById('DOCTORS_GRID')?.instance;
            grid.reload();
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }

            BX.Anb.DoctorsGrid.showMessage(errorMessage);
        });
    },
    addTestBookElement: function () {
        BX.ajax.runComponentAction('anb:doctors.grid', 'addTestBookElement', {
            mode: 'class',
            signedParameters: BX.Anb.DoctorsGrid.signedParams,
            data: {
                bookData: {
                    bookTitle: "Тестовая книга",
                    authors: [
                        1, // идентификатор автора в таблица aholin_author
                        2,
                    ],
                    publishYear: 2025,
                    pageCount: 55,
                    publishDate: '24.07.2025',
                },
            },
        }).then(response => {
            BX.Anb.DoctorsGrid.showMessage('Создана книга с ID=' + response.data.BOOK_ID);
            let grid = BX.Main.gridManager.getById('DOCTORS_GRID')?.instance;
            grid.reload();
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }

            BX.Anb.DoctorsGrid.showMessage(errorMessage);
        });
    },
    createAlternativeTestBookElement: function () {
        BX.ajax.runComponentAction('anb:doctors.grid', 'createTestElement', {
            mode: 'ajax',
            signedParameters: BX.Anb.DoctorsGrid.signedParams,
            data: null,
        }).then(response => {
            BX.Anb.DoctorsGrid.showMessage('Создана книга с ID=' + response.data.BOOK_ID);
            let grid = BX.Main.gridManager.getById('DOCTORS_GRID')?.instance;
            grid.reload();
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }

            BX.Anb.DoctorsGrid.showMessage(errorMessage);
        });
    },
    createTestElementViaModule: function () {
        BX.ajax.runAction(
            'aholin:crmcustomtab.book.BookController.createTestElement',
            {}
        ).then(response => {
            BX.Anb.DoctorsGrid.showMessage('Создана книга с ID=' + response.data.BOOK_ID);
            let grid = BX.Main.gridManager.getById('DOCTORS_GRID')?.instance;
            grid.reload();
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }

            BX.Anb.DoctorsGrid.showMessage(errorMessage);
        });
    },

    addBook: function () {
        BX.Anb.DoctorsGrid.showForm();
    },

    showForm: function () {
        let popup = BX.PopupWindowManager.create('book-add-form', null, {
            content: '<form content="multipart/form-data" id="book-add-form"><input name="bookTitle"><input type="submit" value="Применить"></form>',
            darkMode: true,
            buttons: [
                new BX.PopupWindowButton({
                    text: "Добавить книгу" ,
                    className: "book-form-popup-window-button-accept" ,
                    events: {
                        click: function(){
                            let submit = document.querySelector('#book-add-form input[type="submit"]');
                            let form = document.getElementById('book-add-form');
                            form.addEventListener('submit', function (event) {
                                event.preventDefault();
                                BX.Anb.DoctorsGrid.createBook(event.target);
                            });
                            submit.click();
                            this.popupWindow.close();
                        }
                    }
                }),
                new BX.PopupWindowButton({
                    text: "Закрыть" ,
                    className: "book-form-button-link-cancel" ,
                    events: {
                        click: function(){
                            this.popupWindow.close();
                        }
                    }
                })
            ]
        });
        popup.show();
    },

    createBook: function (form) {
        let data = new FormData(form);
        BX.ajax.runComponentAction('anb:doctors.grid', 'addBook', {
            mode: 'ajax',
            data: data,
        }).then(response => {
            let id = response.data.BOOK_ID;
            BX.Anb.DoctorsGrid.showMessage('Добавлена книга с ID=' + id);
            let grid = BX.Main.gridManager.getById('DOCTORS_GRID')?.instance;
            grid.reload();
        }, reject => {
            let errorMessage = '';
            for (let error of reject.errors) {
                errorMessage += error.message + '\n';
            }

            BX.Anb.DoctorsGrid.showMessage(errorMessage);
        });
    },
}
