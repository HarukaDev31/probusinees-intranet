import random
from collections import deque

def generar_cola_sensores():
    """
    a) Generar una cola con 80 números enteros aleatorios entre 10 y 500
    """
    cola = deque()
    for _ in range(80):
        numero = random.randint(10, 500)
        cola.append(numero)
    return cola

def contar_multiplos_recursivo(cola, index=0, multiplos_4=0, multiplos_5=0):
    """
    b) Método recursivo para contar múltiplos de 4 y 5
    """
    # Caso base: cuando llegamos al final de la cola
    if index >= len(cola):
        # Determinar cuál grupo es mayor
        if multiplos_4 > multiplos_5:
            mensaje = f"Hay {multiplos_4} múltiplos de 4 y {multiplos_5} múltiplos de 5. El mayor grupo es el de múltiplos de 4."
        elif multiplos_5 > multiplos_4:
            mensaje = f"Hay {multiplos_4} múltiplos de 4 y {multiplos_5} múltiplos de 5. El mayor grupo es el de múltiplos de 5."
        else:
            mensaje = f"Hay {multiplos_4} múltiplos de 4 y {multiplos_5} múltiplos de 5. Ambos grupos tienen la misma cantidad."
        
        print(mensaje)
        return multiplos_4, multiplos_5
    
    # Procesar el elemento actual
    elemento = cola[index]
    nuevos_multiplos_4 = multiplos_4 + (1 if elemento % 4 == 0 else 0)
    nuevos_multiplos_5 = multiplos_5 + (1 if elemento % 5 == 0 else 0)
    
    # Llamada recursiva
    return contar_multiplos_recursivo(cola, index + 1, nuevos_multiplos_4, nuevos_multiplos_5)

def ordenamiento_insercion(cola):
    """
    c) Implementar ordenamiento por inserción sobre la cola
    """
    # Convertir la cola a lista para facilitar el ordenamiento
    lista = list(cola)
    
    # Algoritmo de ordenamiento por inserción
    for i in range(1, len(lista)):
        clave = lista[i]
        j = i - 1
        
        # Mover elementos mayores que la clave una posición adelante
        while j >= 0 and lista[j] > clave:
            lista[j + 1] = lista[j]
            j -= 1
        
        lista[j + 1] = clave
    
    # Convertir de vuelta a cola
    cola_ordenada = deque(lista)
    return cola_ordenada

def main():
    print("=== ANÁLISIS DE DATOS DE SENSORES ===\n")
    
    # a) Generar cola con 80 números aleatorios
    print("a) Generando cola con 80 números aleatorios entre 10 y 500...")
    cola_sensores = generar_cola_sensores()
    print(f"Cola generada con {len(cola_sensores)} elementos")
    print("Primeros 10 elementos:", list(cola_sensores)[:10])
    print()
    
    # b) Contar múltiplos de 4 y 5 usando método recursivo
    print("b) Contando múltiplos de 4 y 5 (método recursivo)...")
    multiplos_4, multiplos_5 = contar_multiplos_recursivo(cola_sensores)
    print()
    
    # c) Ordenamiento por inserción
    print("c) Aplicando ordenamiento por inserción...")
    cola_ordenada = ordenamiento_insercion(cola_sensores)
    
    # Imprimir primer y último elemento
    if len(cola_ordenada) > 0:
        primer_elemento = cola_ordenada[0]  # Frente de la cola
        # Para obtener el último elemento de forma correcta, usamos popleft() temporalmente
        # o convertimos a lista para el análisis
        lista_ordenada = list(cola_ordenada)
        ultimo_elemento = lista_ordenada[-1]
        print(f"Primer elemento: {primer_elemento}")
        print(f"Último elemento: {ultimo_elemento}")
        print(f"Rango de valores: {primer_elemento} - {ultimo_elemento}")
    
    print("\n=== ANÁLISIS COMPLETADO ===")

if __name__ == "__main__":
    main() 