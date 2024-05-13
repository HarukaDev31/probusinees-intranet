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
)
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




CREATE FUNCTION intranetprobusiness.get_cbm_total( cbm decimal(10,2),tipo_cliente int)
RETURNS decimal(10,2)
begin
	declare precio decimal(10,2)  default 0;
	declare v_tarifa decimal(10,2) default 0;
	select tarifa  into v_tarifa from carga_consolidada_cbm_tarifas  ccbt
	where (cbm >= ccbt.limite_inf and cbm<=ccbt.limite_sup
	and ccbt.id_tipo_cliente=tipo_cliente) limit 1;
	
	return v_tarifa;
END