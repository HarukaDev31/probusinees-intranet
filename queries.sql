create table carga_consolidada_cotizaciones_cabecera(
ID_Cotizacion int not null auto_increment,
Fe_Creacion date not null,
N_Cliente text,
Empresa text,
Cotizacion decimal(10,2),
ID_Tipo_Cliente int not null,
ID_Cotizacion_Estado int not null,
primary key(ID_Cotizacion),
foreign key(ID_Tipo_cliente) references tipo_cliente(ID_TIPO)
);

create table tipo_carga_consolidada_cotizaciones_status(
ID_Tipo int not null auto_increment,
Nombre varchar(50)
)
create table tipo_cliente(
ID_Tipo int not null auto_increment,
Nombre varchar(255),
primary key(ID_Tipo)
);
select * from carga_consolidada_cotizaciones_cabecera;
insert into tipo_cliente(Nombre) values
("Nuevo");
("Cliente"); 

create table carga_consolidada_cotizaciones_detalle(
ID_Detalle int not null auto_increment,
ID_Cotizacion int not null,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
primary key(ID_Detalle),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)
-- table carga_consolidada_cotizaciones_cabecera
-- ID_Cotizacion int auto increment , Fe_Creacion date ,Empresa text ,CotizacionPrecio decimal(10,2),T.Cliente foreign key 

-- table carga_consolidada_cotizaciones_detalle;
-- ID_Detalle,ID_Cotizacion,CBM_Total,Peso_Total
create table carga_consolidada_cotizaciones_detalle(
ID_Detalle int not null auto_increment,
ID_Cotizacion int not null,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
primary key(ID_Detalle),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)
-- table carga_consolidada_cotizaciones_detalles_proovedor;
-- ID_Proveedor,ID_Detalle,CBM_Total,Peso_Total,URL_Proforma,URL_Packing

create table carga_consolidada_cotizaciones_detalles_proovedor(
ID_Proveedor int not null auto_increment,
ID_Cotizacion int not null ,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
URL_Proforma text,
URL_Packing text,
primary key(ID_Proveedor),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)

-- table carga_consolidada_cotizaciones_detalles_productos;
-- ID_Producto,ID_Cotizacion,ID_Proveedor,URL_Image,URL_Link,Nombre_Comercial,Uso,Cantidad,Valor_Unitario,
create table carga_consolidada_cotizaciones_detalles_producto(
ID_Producto int  not null auto_increment,
ID_Cotizacion int not null,
ID_Proveedor int not null,
URL_Image text not null,
URL_Link text,
Nombre_Comercial varchar(500) not null,
Uso text,
Cantidad decimal(10,2) not null,
Valor_unitario decimal(10,2) not null,
primary key(ID_Producto),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion),
foreign key(ID_Proveedor) references  carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor)
)Status
-- table carga_consolidada_cotizaciones_detalles_tributos;
-- ID_Tributo,ID_Producto,ID_Proveedor,Status
 
create table  carga_consolidada_cotizaciones_detalles_tributo(
ID_Tributo int not null auto_increment,
ID_Producto int not null,
ID_Proveedor int not null,
ID_Cotizacion int not null,
Status enum("Pending","Completed"),
primary key(ID_Tributo),
foreign key(ID_Producto) references carga_consolidada_cotizaciones_detalles_producto(ID_Producto),
foreign key(ID_Proveedor) references carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)
select * from carga_consolidada_cotizaciones_detalle_proveedor;
-- table tipo_carga_consolidada_cotizaciones_tributo
-- ID_Tipo_Tributo, Nombre
se
create table tipo_carga_consolidada_cotizaciones_tributo(
ID_Tipo_Tributo int not null auto_increment,
Nombre varchar(255) not null,
primary key(ID_Tipo_Tributo)
)

insert into tipo_carga_consolidada_cotizaciones_tributo(Nombre)
values
("Ad Valorem"),
("IGV"),
("IPM").
("PERCEPCION"),
("VALORACION").
("ANTIDUMPING");	

SELECT DISTINCT
				MNU.*,
				(SELECT COUNT(*) FROM menu WHERE ID_Padre=MNU.ID_Menu AND Nu_Activo=0) AS Nu_Cantidad_Menu_Padre
				FROM
				menu AS MNU
				JOIN menu_acceso AS MNUACCESS ON(MNU.ID_Menu = MNUACCESS.ID_Menu)
				JOIN grupo_usuario AS GRPUSR ON(GRPUSR.ID_Grupo_Usuario = MNUACCESS.ID_Grupo_Usuario)
				WHERE
				MNU.ID_Padre=0
				AND MNU.Nu_Activo=0
				and grpusr.ID_Grupo=3
				ORDER BY
				MNU.ID_Padre ASC,
				MNU.Nu_Orden;
				

select * from grupo_usuario gu ;
select  * from menu_acceso ma ;
-- save menus with fathers, controller y url en el sidebar , con el idmenu de aqui ,el mnuaccess.id_menu de  menu_acceso 
-- y grupo usuario .id_grupo usuario se crea la sidebar
select * from menu;
select * from menu where No_Menu="Carga Consolidada"

select * from carga_consolidada;
select * from carga_consolidada_pedido_cabecera ccpc ;
select * from pedido_cabecera pc ;
select * from carga_consolidada_pedido_detalle ccpd ;

select * from carga_consolidada_cotizaciones_cabecera cccc ;
alter table carga_consolidada_cotizaciones_cabecera add column Cotizacion_Status enum("Pendiente","Cotizado","Confirmado") not null default "Pendiente"


select * from carga_consolidada_cotizaciones_detalles_proovedor cccdp ;
select * from carga_consolidada_cotizaciones_detalles_producto cccdp ;
select * from carga_consolidada_cotizaciones_detalles_tributo cccdt ;
select * from tipo_carga_consolidada_cotizaciones_tributo tccct ;
insert into tipo_carga_consolidada_cotizaciones_tributo(Nombre)
values
("Ad Valorem"),
("IGV"),
("IPM").
("PERCEPCION"),
("VALORACION").
("ANTIDUMPING");	

SELECT 
    cccc.N_Cliente,
    cccc.Empresa,
    SUM(cccdp.CBM_Total) AS Total_CBM,
    SUM(cccdp.Peso_Total) AS Total_Peso
FROM 
    carga_consolidada_cotizaciones_cabecera AS cccc
JOIN 
    carga_consolidada_cotizaciones_detalles_proovedor AS cccdp ON cccc.ID_Cotizacion = cccdp.ID_Cotizacion
WHERE  
    cccc.ID_Cotizacion = 33
GROUP BY 
    1,2
SELECT 
    cccc.N_Cliente,
    cccc.Empresa
FROM 
    carga_consolidada_cotizaciones_cabecera AS cccc
    
    
select CBM_Total,Peso_Total,
(select json_array(
	json_object(
	'ID_Proveedor',cccdp2.ID_Proveedor,
	'ID_Producto',cccdp2.ID_Producto,
	'URL_Link',cccdp2.URL_Link,
	'Nombre_Comercial',cccdp2.Nombre_Comercial,
	'Uso',cccdp2.Uso,
	'Cantidad',cccdp2.Cantidad,
	'Valor_unitario',if(Valor_unitario is null,0,Valor_unitario)
	) 
) from carga_consolidada_cotizaciones_detalles_producto cccdp2 where cccdp2.ID_Cotizacion=cccdp.ID_Cotizacion)as productos,
(select count(*) from carga_consolidada_cotizaciones_detalles_tributo cccdt where cccdt.Status="Pending") as pending
from carga_consolidada_cotizaciones_detalles_proovedor as cccdp where cccdp.ID_Cotizacion=33;

select * from carga_consolidada_cotizaciones_detalles_proovedor;
select * from carga_consolidada_cotizaciones_detalles_producto cccdp ;
select * from carga_consolidada_cotizaciones_detalles_tributo;
alter table carga_consolidada_cotizaciones_detalles_tributo add column Tr enum("PERCENTAGE","NUMBER","STRING") default "PERCENTAGE"

SELECT 
    cccdprov.ID_Proveedor,
    cccdprov.CBM_Total,
    cccdprov.Peso_Total,
    (
        SELECT 
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'ID_Producto', cccdpro.ID_Producto,
                    'URL_Link', cccdpro.URL_Link,
                    'Nombre_Comercial', cccdpro.Nombre_Comercial,
                    'Uso', cccdpro.Uso,
                    'Cantidad', cccdpro.Cantidad,
                    'Valor_unitario', IFNULL(cccdpro.Valor_unitario, 0),
                    'Tributos_Pendientes', (
                        SELECT 
                            COUNT(*)
                        FROM 
                            carga_consolidada_cotizaciones_detalles_tributo cccdt
                        WHERE 
                            cccdt.ID_Producto = cccdpro.ID_Producto
                            AND cccdt.Status = 'Pending'
                    )
                )
            )
        FROM 
            carga_consolidada_cotizaciones_detalles_producto cccdpro
        WHERE 
            cccdpro.ID_Cotizacion = cccdprov.ID_Cotizacion
            AND cccdpro.ID_Proveedor = cccdprov.ID_Proveedor
    ) AS productos
FROM  
    carga_consolidada_cotizaciones_detalles_proovedor cccdprov
WHERE 
    cccdprov.ID_Cotizacion = 33;

 select * from carga_consolidada_cotizaciones_detalles_tributo;
[{"Uso": "para los pies", "Cantidad": 10000.00, "URL_Link": "https://music.youtube.com/watch?v=zul8B399nzA&list=RDAMVMxQEV9lYHlNY", "ID_Producto": 6, "Valor_unitario": 0, "Nombre_Comercial": "Zapatos", "Tributos_Pendientes": 0}, {"Uso": "313", "Cantidad": 131.00, "URL_Link": "31313", "ID_Producto": 7, "Valor_unitario": 0, "Nombre_Comercial": "1131", "Tributos_Pendientes": 0}]
create table  carga_consolidada_cotizaciones_detalles_tributo(
ID_Tributo int not null auto_increment,
ID_Tipo_Tributo int not null,
ID_Producto int not null,
ID_Proveedor int not null,
ID_Cotizacion int not null,
Status enum("Pending","Completed") default "Pending",
primary key(ID_Tributo),
foreign key(ID_Producto) references carga_consolidada_cotizaciones_detalles_producto(ID_Producto),
foreign key(ID_Proveedor) references carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion),
foreign key(ID_Tipo_Tributo) references tipo_carga_consolidada_cotizaciones_tributo(ID_Tipo_Tributo)
)

-- drop table carga_consolidada_cotizaciones_detalles_tributo;
select * from carga_consolidada_cotizaciones_detalles_tributo;
alter table tipo_carga_consolidada_cotizaciones_tributo add column table_key varchar(50)  default "";

select * from carga_consolidada_cotizaciones_detalles_proovedor cccdp ;
SELECT 
    cccdprov.ID_Proveedor,
    cccdprov.CBM_Total,
    cccdprov.Peso_Total,
    (
        SELECT 
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'ID_Producto', cccdpro.ID_Producto,
                    'URL_Link', cccdpro.URL_Link,
                    'Nombre_Comercial', cccdpro.Nombre_Comercial,
                    'Uso', cccdpro.Uso,
                    'Cantidad', cccdpro.Cantidad,
                    'Valor_unitario', IFNULL(cccdpro.Valor_unitario, 0),
                    'Tributos', (
                        select json_arrayagg(
                        	json_object(
                        	"Tipo_Tributo",tccct.Nombre,
                        	"Key",tccct.table_key,
                        	"Value",cccdt.value,
                        	) 
                        )  from carga_consolidada_cotizaciones_detalles_tributo cccdt 
                        join tipo_carga_consolidada_cotizaciones_tributo tccct  on tccct.ID_Tipo_Tributo=cccdt.ID_Tipo_Tributo
                        where cccdt.ID_Producto=cccdpro.ID_Producto
                        
                    )
                )
            )
        FROM 
            carga_consolidada_cotizaciones_detalles_producto cccdpro
        WHERE 
            cccdpro.ID_Cotizacion = cccdprov.ID_Cotizacion
            AND cccdpro.ID_Proveedor = cccdprov.ID_Proveedor
    ) AS productos
FROM  
    carga_consolidada_cotizaciones_detalles_proovedor cccdprov
WHERE 
    cccdprov.ID_Cotizacion = 33;
   
select Cantidad,Valor_unitario  from carga_consolidada_cotizaciones_detalles_producto cccdp
where ID_Cotizacion =33;

    
create table carga_consolidada_cbm_tarifas(
id_tarifa int  not null auto_increment,
id_tipo_tarifa int not null,
id_tipo_cliente int not null,
limite_inf decimal(10,2) not null,
limite_sup decimal(10,2) not null,
currency  varchar(50) not null default "USD",
tarifa decimal(10,2) not null,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
updated_at DATETIME ,
primary key(id_tarifa)
);
select tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (1 >= ccbt.limite_inf and 1<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=1) limit 1

table carga_consolidada_cbm_tarifas add column type_tarifa int not null default 1
insert into carga_consolidada_cbm_tarifas(id_tipo_tarifa,id_tipo_cliente,limite_inf,limite_sup,tarifa)
values
(1,1,0.1,0.5,250),
(1,1,0.6,1,350),
(2,1,1.1,2,350),
(2,1,2.1,3.0,325),
(2,1,3.1,4,300),
(2,1,4.1,999999,280),
(1,2,0.1,0.5,250),
(1,2,0.6,1,325),
(2,2,1.1,2,325),
(2,2,2.1,3.0,300),
(2,2,3.1,4,275),
(2,2,4.1,999999,250)
;
select * from carga_consolidada_cbm_tarifas;
select get_cbm_total(1,1) as cbm_total;




CREATE FUNCTION get_cbm_total(id_cotizacion int, cbm decimal(10,2),tipo_cliente int)
RETURNS decimal(10,2)
begin
	declare precio decimal(10,2)  default 0;
	declare v_tarifa decimal(10,2) default 0;
	select tarifa  into v_tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (cbm >= ccbt.limite_inf and cbm<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=tipo_cliente) limit 1;
	
	return v_tarifa;
END
SELECT 
    cccdprov.ID_Proveedor,
    cccdprov.CBM_Total,
    cccdprov.Peso_Total,
    (
        SELECT 
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'ID_Producto', cccdpro.ID_Producto,
                    'URL_Link', cccdpro.URL_Link,
                    'Nombre_Comercial', cccdpro.Nombre_Comercial,
                    'Uso', cccdpro.Uso,
                    'Cantidad', cccdpro.Cantidad,
                    'Valor_unitario', IFNULL(cccdpro.Valor_unitario, 0),
                    'Tributos', (
                        select json_arrayagg(
                        	json_object(
                        	"Tipo_Tributo",tccct.Nombre,
                        	"Key",tccct.table_key,
                        	"Value",cccdt.value,
                        	) 
                        )  from carga_consolidada_cotizaciones_detalles_tributo cccdt 
                        join tipo_carga_consolidada_cotizaciones_tributo tccct  on tccct.ID_Tipo_Tributo=cccdt.ID_Tipo_Tributo
                        where cccdt.ID_Producto=cccdpro.ID_Producto
                        
                    )
                )
            )
        FROM 
            carga_consolidada_cotizaciones_detalles_producto cccdpro
        WHERE 
            cccdpro.ID_Cotizacion = cccdprov.ID_Cotizacion
            AND cccdpro.ID_Proveedor = cccdprov.ID_Proveedor
    ) AS productos
FROM  
    carga_consolidada_cotizaciones_detalles_proovedor cccdprov
WHERE 
    cccdprov.ID_Cotizacion = id_cotizacion;
   
select Cantidad,Valor_unitario  from carga_consolidada_cotizaciones_detalles_producto cccdp
where ID_Cotizacion =id_cotizacion;

    
create table carga_consolidada_cbm_tarifas(
id_tarifa int  not null auto_increment,
id_tipo_tarifa int not null,
id_tipo_cliente int not null,
limite_inf decimal(10,2) not null,
limite_sup decimal(10,2) not null,
currency  varchar(50) not null default "USD",
tarifa decimal(10,2) not null,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
updated_at DATETIME ,
primary key(id_tarifa)
);
select tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (1 >= ccbt.limite_inf and 1<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=1) limit 1

table carga_consolidada_cbm_tarifas add column type_tarifa int not null default 1
insert into carga_consolidada_cbm_tarifas(id_tipo_tarifa,id_tipo_cliente,limite_inf,limite_sup,tarifa)
values
(1,1,0.1,0.5,250),
(1,1,0.6,1,350),
(2,1,1.1,2,350),
(2,1,2.1,3.0,325),
(2,1,3.1,4,300),
(2,1,4.1,999999,280),
(1,2,0.1,0.5,250),
(1,2,0.6,1,325),
(2,2,1.1,2,325),
(2,2,2.1,3.0,300),
(2,2,3.1,4,275),
(2,2,4.1,999999,250)
;
CREATE PROCEDURE `get_cotization_tributos_v2`(IN p_id_cotizacion int)
begin
	
	-- valor flete y valor destino
	set @flete=0.6;
	set @destino=0.4;
    -- Obtener la suma de FOB y FOB valorado
    SELECT
        SUM(Cantidad * Valor_unitario) AS sum_fob,
        SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado
    INTO
        @sum_fob,
        @sum_fob_valorado
    FROM
        carga_consolidada_cotizaciones_detalles_producto
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Obtener la suma de CBM total y Peso total
    SELECT
        SUM(CBM_Total) AS cbm_total,
        SUM(Peso_Total) AS peso_total
    INTO
        @cbm_total,
        @peso_total
    FROM
        carga_consolidada_cotizaciones_detalles_proovedor
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Calcular el seguro total
    SET @seguro_total = CASE
    WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(@cbm_total, 1) * @flete)) > 5000 THEN 100
    ELSE 50
END;
    -- Calcular el CIF total
    SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete);

    -- Calcular el CIF valorado total
    SET @cif_valorado_total = CASE
        WHEN @sum_fob_valorado = 0 THEN 0
        ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
    END;

    SELECT
        cccdp.Cantidad,
        cccdp.Valor_unitario,
        (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS valoracion,
        cccdp.Cantidad * cccdp.Valor_unitario AS fob,
        cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS fob_valorado,
        @sum_fob AS sum_fob,
        @sum_fob_valorado AS sum_fob_valorado,
        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS distribucion,
        @cbm_total AS cbm_total,
        @peso_total AS peso_total,
        ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) AS flete,
        @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete) AS cfr_total,
        ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS cfr,
        CASE
            WHEN @sum_fob_valorado <> 0 THEN
                ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                    ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
            ELSE 0
        END AS cfr_valorado,
        
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
        END AS cfr_valorado_total,
        @seguro_total AS seguro_total,
        @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS seguro,
        @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
        ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS valor_cif,
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        END AS valor_cif_valorado,
        @cif_total AS cif_total,
        @cif_valorado_total AS cif_valorado_total,
        (SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS ad_valorem,
        (SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS igv,
        (SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS ipm,
        (SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS percepcion,
        (SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS antidumping,
       ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS advalorem,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS igv,
ROUND((
    CASE
        WHEN @sum_fob_valorado  <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ipm,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))
end ),2) as percepcion,
ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_de_envio,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )+ CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END 
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))+@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
	end ),2)+ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_total,
    (SELECT get_tribute_value(cccdp.ID_Producto, 6)) as antidumping
from
    
        carga_consolidada_cotizaciones_detalles_producto cccdp
    WHERE
        cccdp.ID_Cotizacion = p_id_cotizacion;
END

CREATE  FUNCTION `get_cbm_total`( cbm decimal(10,2),tipo_cliente int) RETURNS decimal(10,2)
begin
	declare precio decimal(10,2)  default 0;
	declare v_tarifa decimal(10,2) default 0;
	select tarifa  into v_tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (cbm >= ccbt.limite_inf and cbm<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=tipo_cliente) limit 1;
	
	return v_tarifa;
END

CREATE  FUNCTION `get_cbm_total`( cbm decimal(10,2),tipo_cliente int) RETURNS decimal(10,2)
begin
	declare precio decimal(10,2)  default 0;
	declare v_tarifa decimal(10,2) default 0;
	select tarifa  into v_tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (cbm >= ccbt.limite_inf and cbm<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=tipo_cliente) limit 1;
	
	return v_tarifa;
END
CREATE FUNCTION intranetprobusiness.get_taxes_calc(
    p_producto_id INT,
    p_cotizacion_id INT,
    p_tributo_id INT,
    v_valor_cif decimal(10,2)
)
RETURNS DECIMAL(10, 2)
BEGIN
    DECLARE v_distribucion DECIMAL(10, 2);
    DECLARE v_ad_honorem DECIMAL(10, 2);
    DECLARE v_igv DECIMAL(10, 2);
    DECLARE v_ipm DECIMAL(10, 2);
    DECLARE v_percepcion DECIMAL(10, 2);
    DECLARE v_valor_tributo DECIMAL(10, 2);


    -- Obtener los valores de los tributos
    SELECT 
        get_tribute_value(p_producto_id, 1),
        get_tribute_value(p_producto_id, 2),
        get_tribute_value(p_producto_id, 3),
        get_tribute_value(p_producto_id, 4)
    INTO 
        v_ad_honorem,
        v_igv,
        v_ipm,
        v_percepcion;

    -- Calcular el valor del CIF

    -- Calcular el valor del tributo según el tipo de tributo
    CASE p_tributo_id
        WHEN 1 THEN
            SET v_valor_tributo = v_valor_cif * v_ad_honorem / 100;
        WHEN 2 THEN
            SET v_valor_tributo = (v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100;
        WHEN 3 THEN
            SET v_valor_tributo = (v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100;
        WHEN 4 THEN
            SET v_valor_tributo = (v_valor_cif + (v_valor_cif * v_ad_honorem / 100)
                + ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100)
                + ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100))
                * v_percepcion / 100;
        WHEN -1 THEN
            SET v_valor_tributo = (v_valor_cif * v_ad_honorem / 100) +
             ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100)+
              ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100)+
              ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)
                + ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100)
                + ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100))
                * v_percepcion / 100);
        ELSE
            SET v_valor_tributo = 0;
    END CASE;

    RETURN v_valor_tributo;
END
CREATE DEFINER=`root`@`localhost` PROCEDURE `intranet_probusiness`.`get_cotization_tributos_v2`(IN p_id_cotizacion int)
begin
	
	-- valor flete y valor destino
	set @flete=0.6;
	set @destino=0.4;
    -- Obtener la suma de FOB y FOB valorado
    SELECT
        SUM(Cantidad * Valor_unitario) AS sum_fob,
        SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado
    INTO
        @sum_fob,
        @sum_fob_valorado
    FROM
        carga_consolidada_cotizaciones_detalles_producto
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Obtener la suma de CBM total y Peso total
    SELECT
        SUM(CBM_Total) AS cbm_total,
        SUM(Peso_Total) AS peso_total
    INTO
        @cbm_total,
        @peso_total
    FROM
        carga_consolidada_cotizaciones_detalles_proovedor
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Calcular el seguro total
    SET @seguro_total = CASE
    WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(@cbm_total, 1) * @flete)) > 5000 THEN 100
    ELSE 50
END;
    -- Calcular el CIF total
    SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete);

    -- Calcular el CIF valorado total
    SET @cif_valorado_total = CASE
        WHEN @sum_fob_valorado = 0 THEN 0
        ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
    END;

    select
    	0 Peso,
    	(SELECT get_cbm_total(@cbm_total, 1)),
    	cccdp.Valor_Unitario,
    	(SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valoracion,
        cccdp.Cantidad,
        cccdp.Cantidad * cccdp.Valor_unitario AS Valor_FOB,
        cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valor_FOB_Valorado,
        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Distribucion,
        ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) AS Flete,
        ROUND(((SELECT get_cbm_total(@cbm_total, 1) * @flete*ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) )+(cccdp.Cantidad*cccdp.Valor_Unitario) ),2),
        CASE
            WHEN @sum_fob_valorado <> 0 THEN
                ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                    ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
            ELSE 0
        END AS Valor_CFR,
        @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Seguro,
		@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
        ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS Valor_CIF,
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        END as Valor_CIF_Valorado,
        @sum_fob AS sum_fob,
        @sum_fob_valorado AS sum_fob_valorado,
        @cbm_total AS cbm_total,
        @peso_total AS peso_total,
        @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete) AS cfr_total,
        ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS cfr,
        
        
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
        END AS cfr_valorado_total,
        @seguro_total AS seguro_total,
       
        
        @cif_total AS cif_total,
        @cif_valorado_total AS cif_valorado_total,
        (SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS ad_valorem,
        (SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS igv,
        (SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS ipm,
        (SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS percepcion,
        (SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS antidumping,
       ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ad_valorem_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS igv_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado  <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ipm_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))
end ),2) as percepcion_value,
ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_de_envio,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )+ CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END 
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))+@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
	end ),2)+ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_total
from
    
        carga_consolidada_cotizaciones_detalles_producto cccdp
    WHERE
        cccdp.ID_Cotizacion = p_id_cotizacion;
END
CREATE DEFINER=`root`@`localhost` PROCEDURE `intranetprobusiness`.`get_cotization_tributos_v2`(IN p_id_cotizacion int)
begin
	
	-- valor flete y valor destino
	set @flete=0.6;
	set @destino=0.4;
    -- Obtener la suma de FOB y FOB valorado
    SELECT
        SUM(Cantidad * Valor_unitario) AS sum_fob,
        SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado,
        SUM(Cantidad) as total_cantidad
    INTO
        @sum_fob,
        @sum_fob_valorado,
        @total_cantidad
    FROM
        carga_consolidada_cotizaciones_detalles_producto
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Obtener la suma de CBM total y Peso total
    SELECT
        SUM(CBM_Total) AS cbm_total,
        SUM(Peso_Total) AS peso_total
    INTO
        @cbm_total,
        @peso_total
    FROM
        carga_consolidada_cotizaciones_detalles_proovedor
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Calcular el seguro total
    SET @seguro_total = CASE
    WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(@cbm_total, 1) * @flete)) > 5000 THEN 100
    ELSE 50
END;
    -- Calcular el CIF total
    SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete);

    -- Calcular el CIF valorado total
    SET @cif_valorado_total = CASE
        WHEN @sum_fob_valorado = 0 THEN 0
        ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
    END;

    select
    	0 Peso,
    	(SELECT get_cbm_total(@cbm_total, 1)) Total_CBM,
    	cccdp.Valor_Unitario,
    	(SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valoracion,
    	cccdp.Cantidad,
        cccdp.Cantidad * cccdp.Valor_unitario AS Valor_FOB,
        cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valor_FOB_Valorado,
        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Distribucion,
        ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) AS Flete,
        ROUND(((SELECT get_cbm_total(@cbm_total, 1) * @flete*ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) )+(cccdp.Cantidad*cccdp.Valor_Unitario) ),2) Valor_CFR,
        CASE
            WHEN @sum_fob_valorado <> 0 THEN
                ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                    ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
            ELSE 0
        END AS Valor_CFR_Valorizado,
        @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Seguro,
		@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
        ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS Valor_CIF,
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        END as Valor_CIF_Valorado,
        @sum_fob AS sum_fob,
        @sum_fob_valorado AS sum_fob_valorado,
        @cbm_total AS cbm_total,
        @peso_total AS peso_total,
        @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete) AS cfr_total,
        ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS cfr,
        
        
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
        END AS cfr_valorado_total,
        @seguro_total AS seguro_total,
       
        
        @cif_total AS cif_total,
        @cif_valorado_total AS cif_valorado_total,
        (SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS ad_valorem,
        (SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS igv,
        (SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS ipm,
        (SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS percepcion,
        (SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS antidumping,
       ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ad_valorem_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS igv_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado  <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ipm_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))
end ),2) as percepcion_value,
ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_de_envio,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )+ CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END 
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))+@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(@cbm_total, 1)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
	end ),2)+ROUND((SELECT get_cbm_total(@cbm_total, 1)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_total,
	(select Peso_total from carga_consolidada_cotizaciones_detalles_proovedor where ID_Cotizacion=p_id_cotizacion) as Peso_Total,
	@total_cantidad as Total_Cantidad,
	ROUND((SELECT get_cbm_total(@cbm_total, 1))) Servicio,
	cccdp.Nombre_Comercial
from
        carga_consolidada_cotizaciones_detalles_producto cccdp
    WHERE
        cccdp.ID_Cotizacion = p_id_cotizacion;
END


CREATE FUNCTION probussiness.get_tribute_value(
p_id_producto int ,tipo_tributo int
)
RETURNS INT
begin
	declare v_value int default 0;
		select value into v_value from carga_consolidada_cotizaciones_detalles_tributo cccdt where ID_Producto =p_id_producto
		and ID_Tipo_Tributo =tipo_tributo
		;
	return v_value;
END

CREATE PROCEDURE probussiness.get_cotization_tributos_v2( IN p_id_cotizacion int ,in v_t_cliente int)
begin
	
	-- valor flete y valor destino
	set @flete=0.6;
	set @destino=0.4;
    -- Obtener la suma de FOB y FOB valorado
    SELECT
        SUM(Cantidad * Valor_unitario) AS sum_fob,
        SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado,
        SUM(Cantidad) as total_cantidad
    INTO
        @sum_fob,
        @sum_fob_valorado,
        @total_cantidad
    FROM
        carga_consolidada_cotizaciones_detalles_producto
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Obtener la suma de CBM total y Peso total
    SELECT
        SUM(CBM_Total) AS cbm_total,
        SUM(Peso_Total) AS peso_total
    INTO
        @cbm_total,
        @peso_total
    FROM
        carga_consolidada_cotizaciones_detalles_proovedor
    WHERE
        ID_Cotizacion = p_id_cotizacion;

    -- Calcular el seguro total
    SET @seguro_total = CASE
    WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)) > 5000 THEN 100
    ELSE 50
END;
    -- Calcular el CIF total
    SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete);

    -- Calcular el CIF valorado total
    SET @cif_valorado_total = CASE
        WHEN @sum_fob_valorado = 0 THEN 0
        ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)
    END;

    select
    	0 Peso,
    	(SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) Total_CBM,
    	cccdp.Valor_Unitario,
    	(SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valoracion,
    	cccdp.Cantidad,
        cccdp.Cantidad * cccdp.Valor_unitario AS Valor_FOB,
        cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valor_FOB_Valorado,
        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Distribucion,
        ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) AS Flete,
        ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete*ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) )+(cccdp.Cantidad*cccdp.Valor_Unitario) ),2) Valor_CFR,
        CASE
            WHEN @sum_fob_valorado <> 0 THEN
                ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                    ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
            ELSE 0
        END AS Valor_CFR_Valorizado,
        @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Seguro,
		@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
        ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS Valor_CIF,
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        END as Valor_CIF_Valorado,
        @sum_fob AS sum_fob,
        @sum_fob_valorado AS sum_fob_valorado,
        @cbm_total AS cbm_total,
        @peso_total AS peso_total,
        @sum_fob + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) AS cfr_total,
        ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS cfr,
        
        
        CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @sum_fob_valorado + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)
        END AS cfr_valorado_total,
        @seguro_total AS seguro_total,
       
        
        @cif_total AS cif_total,
        @cif_valorado_total AS cif_valorado_total,
        (SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS ad_valorem,
        (SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS igv,
        (SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS ipm,
        (SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS percepcion,
        (SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS antidumping,
       ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ad_valorem_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            2,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS igv_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado  <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            3,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				)
            )
    END
), 2) AS ipm_value,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            4,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))
end ),2) as percepcion_value,
ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_de_envio,
ROUND((
    CASE
        WHEN @sum_fob_valorado <> 0 THEN
            (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(@cbm_total, 1) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END )
            )+ CASE
            WHEN @sum_fob_valorado = 0 THEN 0
            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                CASE
                    WHEN @sum_fob_valorado <> 0 THEN
                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                    ELSE 0
                END
        	END 
        ELSE
             (select get_taxes_calc(
            cccdp.ID_Producto,
            cccdp.ID_Cotizacion,
            -1,
             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
				
            ))+@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
	end ),2)+ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_total,
	(select Peso_total from carga_consolidada_cotizaciones_detalles_proovedor where ID_Cotizacion=p_id_cotizacion) as Peso_Total,
	@total_cantidad as Total_Cantidad,
	ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente))) Servicio,
	cccdp.Nombre_Comercial
from
        carga_consolidada_cotizaciones_detalles_producto cccdp
    WHERE
        cccdp.ID_Cotizacion = p_id_cotizacion;
END
CREATE DEFINER=`root`@`localhost` FUNCTION `probussiness`.`get_cbm_total`(id_cotizacion int, cbm decimal(10,2),tipo_cliente int) RETURNS decimal(10,2)
begin
	declare precio decimal(10,2)  default 0;
	declare v_tarifa decimal(10,2) default 0;
	select tarifa  into v_tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (cbm >= ccbt.limite_inf and cbm<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=tipo_cliente) limit 1;
	
	return v_tarifa;
END     

create table carga_consolidada_cotizaciones_detalle(
ID_Detalle int not null auto_increment,
ID_Cotizacion int not null,
DNI varchar(20) not null,
Nombres varchar(255) not null,
Apellidos varchar(255) not null,
Telefono varchar(20) not null,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
primary key(ID_Detalle),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)

-- final get_cotization_tributos_v2
CREATE DEFINER=`root`@`localhost` PROCEDURE `probussiness`.`get_cotization_tributos_v2`( IN p_id_cotizacion int )
begin
		declare v_t_cliente int default 1;

		-- valor flete y valor destino
		set @flete=0.6;
		set @destino=0.4;
		select ID_Tipo_Cliente into v_t_cliente from carga_consolidada_cotizaciones_cabecera cccc where cccc.ID_Cotizacion=p_id_cotizacion;
	    -- Obtener la suma de FOB y FOB valorado
	    SELECT
	        SUM(Cantidad * Valor_unitario) AS sum_fob,
	        SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado,
	        SUM(Cantidad) as total_cantidad
	    INTO
	        @sum_fob,
	        @sum_fob_valorado,
	        @total_cantidad
	    FROM
	        carga_consolidada_cotizaciones_detalles_producto
	    WHERE
	        ID_Cotizacion = p_id_cotizacion;
	
	    -- Obtener la suma de CBM total y Peso total
	    SELECT
	        SUM(CBM_Total) AS cbm_total,
	        SUM(Peso_Total) AS peso_total
	    INTO
	        @cbm_total,
	        @peso_total
	    FROM
	        carga_consolidada_cotizaciones_detalles_proovedor
	    WHERE
	        ID_Cotizacion = p_id_cotizacion;
	
	    -- Calcular el seguro total
	    SET @seguro_total = CASE
	    WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)) > 5000 THEN 100
	    ELSE 50
	END;
	    -- Calcular el CIF total
	    SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete);
	
	    -- Calcular el CIF valorado total
	    SET @cif_valorado_total = CASE
	        WHEN @sum_fob_valorado = 0 THEN 0
	        ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)
	    END;
	
	    select
	    	cccdp.Nombre_Comercial,
	    	0 Peso,
	    	(SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) Total_CBM,
	    	cccdp.Valor_Unitario,
	    	(SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valoracion,
	    	cccdp.Cantidad,
	        cccdp.Cantidad * cccdp.Valor_unitario AS Valor_FOB,
	        cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valor_FOB_Valorado,
	        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Distribucion,
	        ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) AS Flete,
	        ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete*ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) )+(cccdp.Cantidad*cccdp.Valor_Unitario) ),2) Valor_CFR,
	        CASE
	            WHEN @sum_fob_valorado <> 0 THEN
	                (((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	               ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)))
	            ELSE 0
	        END AS Valor_CFR_Valorizado,
	        @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Seguro,
			@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	        ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS Valor_CIF,
	        CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
	                    ELSE 0
	                END
	        END as Valor_CIF_Valorado,
	        @sum_fob AS sum_fob,
	        @sum_fob_valorado AS sum_fob_valorado,
	        @cbm_total AS cbm_total,
	        @peso_total AS peso_total,
	        @sum_fob + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) AS cfr_total,
	        ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS cfr,
	        
	        
	        CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @sum_fob_valorado + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)
	        END AS cfr_valorado_total,
	        @seguro_total AS seguro_total,
	       
	        
	        @cif_total AS cif_total,
	        @cif_valorado_total AS cif_valorado_total,
	        (SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS ad_valorem,
	        (SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS igv,
	        (SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS ipm,
	        (SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS percepcion,
	        (SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS antidumping,
	       ROUND((
	    CASE
	        WHEN @sum_fob_valorado <> 0 THEN
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            1,
	             CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
	                    ELSE 0
	                END
	        	END )
	            )
	        ELSE
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            1,
	             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
	             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
					)
	            )
	    END
	), 2) AS ad_valorem_value,
	ROUND((
	    CASE
	        WHEN @sum_fob_valorado <> 0 THEN
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            2,
	             CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
	                    ELSE 0
	                END
	        	END )
	            )
	        ELSE
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            2,
	             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
	             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
					)
	            )
	    END
	), 2) AS igv_value,
	ROUND((
	    CASE
	        WHEN @sum_fob_valorado  <> 0 THEN
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            3,
	             CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
	                    ELSE 0
	                END
	        	END )
	            )
	        ELSE
	             (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            3,
	             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
	             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
					)
	            )
	    END
	), 2) AS ipm_value,
	ROUND((
	    CASE
	        WHEN @sum_fob_valorado <> 0 THEN
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            4,
	             CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
	                    ELSE 0
	                END
	        	END )
	            )
	        ELSE
	             (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            4,
	             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
	             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
					
	            ))
	end ),2) as percepcion_value,
	ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_de_envio,
	ROUND((
	    CASE
	        WHEN @sum_fob_valorado <> 0 THEN
	            (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            -1,
	             CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
	                    ELSE 0
	                END
	        	END )
	            )+ CASE
	            WHEN @sum_fob_valorado = 0 THEN 0
	            ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
	                CASE
	                    WHEN @sum_fob_valorado <> 0 THEN
	                        ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
	                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
	                            ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
	                    ELSE 0
	                END
	        	END 
	        ELSE
	             (select get_taxes_calc(
	            cccdp.ID_Producto,
	            cccdp.ID_Cotizacion,
	            -1,
	             @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
	             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
					
	            ))+@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
	             * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
		end ),2)+ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_total,
		(select Peso_total from carga_consolidada_cotizaciones_detalles_proovedor where ID_Cotizacion=p_id_cotizacion and cccdp.ID_Proveedor=ID_Proveedor) as Peso_Total,
		@total_cantidad as Total_Cantidad,
		ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente))) Servicio
	from
	        carga_consolidada_cotizaciones_detalles_producto cccdp
	    WHERE
	        cccdp.ID_Cotizacion = p_id_cotizacion;
	END