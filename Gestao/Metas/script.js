const Consulta_Planos_Disponiveis = async () => {
        $('#loadingModal').modal('show');
        try {
            const data = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Planos_Disponiveis',
                }
            });
            await Tabela_Planos(data);
            $('#ModalPlanosDisponiveis').modal('show')
        } catch (error) {
            console.error('Erro ao consultar chamados:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    };

    function FormatarData(date) {
        const parts = date.split('/');
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }

    const Consulta_Cronograma_Fase = async (codPlano, codFase) => {
        $('#loadingModal').modal('show');
        try {
            const data = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Cronograma_Fase',
                    codPlano: codPlano,
                    codFase: codFase
                }
            });
            console.log(data[0]['DataInicio'])
            $('#data-inicial-cronograma').val(FormatarData(data[0]['DataInicio']));
            $('#data-final-cronograma').val(FormatarData(data[0]['DataFim']));
            $('#modalCronogramas').modal('show')
        } catch (error) {
            console.error('Erro ao consultar chamados:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    };

    function Tabela_Planos(listaPlanos) {
        if ($.fn.DataTable.isDataTable('#table-planos-disponiveis')) {
            $('#table-planos-disponiveis').DataTable().destroy();
        }

        const tabela = $('#table-planos-disponiveis').DataTable({
            searching: true,
            paging: false,
            lengthChange: false,
            info: false,
            pageLength: 10,
            data: listaPlanos,
            columns: [{
                    data: '01- Codigo Plano'
                },
                {
                    data: '02- Descricao do Plano'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <div" style="display: flex; justify-content: space-around; align-items: center; height: 100%;">
                            <button class="btn" style="background-color: var(--CorMenu); color: var(--Branco);" onclick="$('#codigoPlano').val('${row['01- Codigo Plano']}'); ConsultaLote(); $('#ModalPlanosDisponiveis').modal('hide')">Selecionar</button>
                        </div>
                    `;
                    }
                }
            ],
            language: {
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
        });

        // Associar a busca ao campo de pesquisa do modal
        $('#search-planos').on('keyup', function() {
            tabela.search(this.value).draw();
        });
    }

    $(document).ready(() => {

        $('#NomeRotina').text('Metas');
        $('#loadingModal').modal('hide')
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('data-inicial').value = hoje;
        document.getElementById('data-final').value = hoje;
    });


    async function ConsultarMetas(congelado) {
        $('#loadingModal').modal('show');
        try {
            const dados = {
                codigoPlano: $('#codigoPlano').val(),
                arrayCodLoteCsw: [$('#SelectLote').val()],
                dataMovFaseIni: $('#data-inicial').val(),
                dataMovFaseFim: $('#data-final').val(),
                congelado: congelado
            };

            const requestData = {
                acao: "Consultar_Metas",
                dados: dados
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            CriarTabelaMetas(response[0]['1-Detalhamento']);
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    function CriarTabelaMetas(listaMetas) {
        if ($.fn.DataTable.isDataTable('#table')) {
            $('#table').DataTable().destroy();
        }

        const tabela = $('#table').DataTable({
            paging: false,
            info: false,
            searching: true,
            colReorder: true,
            data: listaMetas,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            ordering: false,
            dom: 'Bfrtip',
            buttons: [{
                text: '<i class="fa-solid fa-filter"></i>',
                className: 'ButtonModal',
                action: function(e, dt, node, config) {
                    $('#filtrosModal').modal('show');
                },
                attr: {
                    title: 'Filtros'
                }
            }],
            columns: [{
                    data: 'apresentacao',
                    visible: false
                },
                {
                    data: 'codFase',
                    visible: false
                },
                {
                    data: 'nomeFase'
                },
                {
                    data: 'previsao',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'FaltaProgramar',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Carga Atual',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Fila',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Falta Produzir',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'dias',
                    render: function(data, type, row) {
                        return `<span class="diasClicado" data-fase="${row.codFase}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                    }
                },
                {
                    data: 'Meta Dia',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Realizado',
                    render: function(data, type, row) {
                        const realizado = parseInt(data);
                        const meta = parseInt(row['Meta Dia']);
                        let icon = '';

                        if (realizado < meta) {
                            icon = ' <i class="fas fa-arrow-down" style="color: red; float: right;"></i>';
                        } else if (realizado > meta) {
                            icon = ' <i class="fas fa-arrow-up" style="color: green; float: right;"></i>';
                        }

                        return realizado.toLocaleString() + icon;
                    }
                },
                {
                data: null, // Coluna para a porcentagem
                render: function(data, type, row) {
                    const realizado = parseInt(row['Realizado']);
                    const meta = parseInt(row['Meta Dia']);
                    if (meta === 0) {
                        return '0%';
                    }
                    const percent = (realizado / meta * 100).toFixed(2);
                    return `${percent}%`;
                }
            }
            ],
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<',
                    next: '>',
                    last: 'Última',
                },
            },
        });

        $('#search').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        $('#table').on('click', '.diasClicado', function() {
            const codFase = $(this).data('fase');
            console.log(codFase)
            Consulta_Cronograma_Fase($('#codigoPlano').val(), codFase);
        });
    }


    async function ConsultaLote() {
        if ($('#codigoPlano').val() === '') {
            Mensagem('Campo Vazio', 'warning')
        } else {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Lotes',
                        plano: $('#codigoPlano').val()
                    }
                });

                const $select = $('#SelectLote');
                $select.empty();
                $select.append('<option value="">Selecione o Lote:</option>');

                response.forEach(item => {
                    $select.append(`<option value="${item.lote}">${item.lote} - ${item.nomelote}</option>`);
                });
                // Ativar o Select2 para o elemento
                $('#SelectLote').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    height: '30px',
                });
                $('.select2-selection__rendered').addClass('form-control')

                $('#DescPlano').val(response[0]['nomelote']);
                $('#selects').removeClass('d-none');
            } catch (error) {
                console.error('Erro ao consultar chamados:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        }
    }



    function SelecaoLote() {

        ConsultarMetas(false);
        $('#table').removeClass('d-none');
        $('#campo-search').removeClass('d-none');

    }
