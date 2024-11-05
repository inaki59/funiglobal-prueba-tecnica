-- 1. Identificar productos que tienen imágenes duplicadas 
SELECT product_id, image_id, COUNT(*) AS duplicate_count
FROM product_images
GROUP BY product_id, image_id
HAVING duplicate_count > 1;

-- 2. Eliminar imágenes duplicadas, dejando solo una imagen única 
DELETE FROM product_images
WHERE product_image_id NOT IN (
    SELECT MIN(product_image_id)
    FROM product_images
    GROUP BY product_id, image_id
);

-- 3. Verificar y corregir las imágenes primarias (cover = true) para que cada producto tenga solo una imagen primaria
-- Paso 3.1: Seleccionar productos con múltiples imágenes primarias
SELECT product_id
FROM product_images
WHERE cover = true
GROUP BY product_id
HAVING COUNT(*) > 1;

-- Paso 3.2: Actualizar imágenes duplicadas para que solo una quede como primaria (la más reciente)
UPDATE product_images AS pi
JOIN (
    SELECT product_id, MAX(product_image_id) AS latest_primary_id
    FROM product_images
    WHERE cover = true
    GROUP BY product_id
) AS primary_images ON pi.product_id = primary_images.product_id
SET pi.cover = IF(pi.product_image_id = primary_images.latest_primary_id, true, false);


