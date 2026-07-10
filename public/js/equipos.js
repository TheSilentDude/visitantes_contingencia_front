(function(){
                            const maxEquipos = 5;
                            let contador = 0;

                            const tipoInput = document.getElementById('tipo_equipo_input');
                            const serialInput = document.getElementById('serial_equipo_input');
                            const marcaInput = document.getElementById('marca_input');
                            const btnAgregar = document.getElementById('btnAgregarEquipo');
                            const lista = document.getElementById('listaEquipos');
                            const hiddenContainer = document.getElementById('equiposHiddenContainer');

                            function crearElementoOculto(name, value) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = name;
                                input.value = value;
                                return input;
                            }

                            function actualizarIndices() {
                                // actualizar numeración visual
                                Array.from(lista.children).forEach((li, idx) => {
                                    const spanIndex = li.querySelector('.indiceEquipo');
                                    if (spanIndex) spanIndex.textContent = (idx + 1) + '. ';
                                });
                            }

                            btnAgregar.addEventListener('click', function(){
                                const tipo = tipoInput.value.trim();
                                const serial = serialInput.value.trim();
                                const marca = marcaInput.value.trim();

                                if (!tipo && !serial && !marca) {
                                    alert('Ingrese al menos un dato del equipo.');
                                    return;
                                }

                                if (contador >= maxEquipos) {
                                    alert('Ha alcanzado el límite de ' + maxEquipos + ' equipos.');
                                    return;
                                }

                                contador++;

                                // crear elemento visible en la lista
                                const li = document.createElement('li');
                                li.className = 'list-group-item d-flex justify-content-between align-items-center';

                                const infoSpan = document.createElement('span');
                                infoSpan.innerHTML = '<strong class="indiceEquipo">' + contador + '. </strong>' +
                                                    '<span class="textoEquipo">' + (tipo || '-') + ' / ' + (marca || '-') + ' / ' + (serial || '-') + '</span>';

                                // botones eliminar
                                const btnGroup = document.createElement('div');
                                const btnEliminar = document.createElement('button');
                                btnEliminar.type = 'button';
                                btnEliminar.className = 'btn btn-sm btn-danger';
                                btnEliminar.textContent = 'Eliminar';

                                btnEliminar.addEventListener('click', function(){
                                    // remover inputs ocultos asociados
                                    const inputs = li._hiddenInputs || [];
                                    inputs.forEach(i => hiddenContainer.removeChild(i));
                                    // remover elemento de lista
                                    lista.removeChild(li);
                                    contador--;
                                    actualizarIndices();
                                });

                                btnGroup.appendChild(btnEliminar);
                                li.appendChild(infoSpan);
                                li.appendChild(btnGroup);

                                // crear inputs ocultos para envío
                                const hiddenTipo = crearElementoOculto('tipo_equipo[]', tipo);
                                const hiddenSerial = crearElementoOculto('serial_equipo[]', serial);
                                const hiddenMarca = crearElementoOculto('marca[]', marca);

                                // almacenar referencia para eliminarlos después
                                li._hiddenInputs = [hiddenTipo, hiddenSerial, hiddenMarca];

                                hiddenContainer.appendChild(hiddenTipo);
                                hiddenContainer.appendChild(hiddenSerial);
                                hiddenContainer.appendChild(hiddenMarca);

                                lista.appendChild(li);

                                // limpiar campos visibles
                                tipoInput.value = '';
                                serialInput.value = '';
                                marcaInput.value = '';

                                if (contador >= maxEquipos) {
                                    btnAgregar.disabled = true;
                                }
                            });

                        })();