-- Script para crear los menús de Órdenes en la base de datos
-- Ejecutar este script en tu base de datos para agregar la nueva sección

-- Insertar el menú padre "Órdenes"
INSERT INTO menu (ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, No_Menu_China, show_father)
VALUES (0, 6, 'Órdenes', '#', 'OrdersController', 'fa fa-list', 1, 0, 0, 0, 'Orders', 1);

-- Obtener el ID del menú padre recién insertado
SET @menu_padre_id = LAST_INSERT_ID();

-- Insertar el submenú "Cotizaciones"
INSERT INTO menu (ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, No_Menu_China, show_father)
VALUES (@menu_padre_id, 1, 'Cotizaciones', 'OrdersController/listar', 'OrdersController', 'fa fa-file-excel', 0, 0, 0, 0, 'Orders', 1);

-- Insertar el submenú "Confirmados"
INSERT INTO menu (ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, No_Menu_China, show_father)
VALUES (@menu_padre_id, 2, 'Confirmados', 'OrdersController/listarConfirmados', 'OrdersController', 'fa fa-check-circle', 0, 0, 0, 0, 'Orders', 1);

-- Insertar el submenú "Pendientes"
INSERT INTO menu (ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, No_Menu_China, show_father)
VALUES (@menu_padre_id, 3, 'Pendientes', 'OrdersController/listarPendientes', 'OrdersController', 'fa fa-clock', 0, 0, 0, 0, 'Orders', 1);

-- Insertar el submenú "Observados"
INSERT INTO menu (ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, No_Menu_China, show_father)
VALUES (@menu_padre_id, 4, 'Observados', 'OrdersController/listarObservados', 'OrdersController', 'fa fa-eye', 0, 0, 0, 0, 'Orders', 1);

-- Insertar el submenú "Rechazados"
INSERT INTO menu (ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, No_Menu_China, show_father)
VALUES (@menu_padre_id, 5, 'Rechazados', 'OrdersController/listarRechazados', 'OrdersController', 'fa fa-times-circle', 0, 0, 0, 0, 'Orders', 1);

-- Verificar que los menús se insertaron correctamente
SELECT 
    m1.ID_Menu as ID_Padre,
    m1.No_Menu as Menu_Padre,
    m2.ID_Menu as ID_Hijo,
    m2.No_Menu as Menu_Hijo,
    m2.No_Menu_Url as URL_Hijo
FROM menu m1
LEFT JOIN menu m2 ON m1.ID_Menu = m2.ID_Padre
WHERE m1.No_Menu = 'Órdenes'
ORDER BY m2.Nu_Orden; 