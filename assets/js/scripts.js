$(document).ready(function() {

        // Formatação do CPF enquanto o usuário digita
        $('#clienteCpf').on('input', function() {
            var cpf = $(this).val().replace(/\D/g, ''); // Remove todos os não-dígitos
            if (cpf.length > 3 && cpf.length <= 6) {
                cpf = cpf.replace(/^(\d{3})(\d{1,3})/, '$1.$2');
            } else if (cpf.length > 6 && cpf.length <= 9) {
                cpf = cpf.replace(/^(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (cpf.length > 9) {
                cpf = cpf.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            }
            $(this).val(cpf);
        });

            // Remoção de formatação ao enviar o formulário
        $('form').on('submit', function() {
            var cpf = $('#clienteCpf').val().replace(/\D/g, ''); // Remove todos os não-dígitos
            $('#clienteCpf').val(cpf);
        });

    // Captura o evento de clique no botão de exclusão
    $('.delete-client').click(function(e) {
        e.preventDefault(); // Evita o comportamento padrão de seguir o link

        // Obtém o ID do cliente a ser excluído do atributo data-client-id
        var clienteId = $(this).data('client-id');

        // Confirmação antes de excluir (opcional)
        if (!confirm('Tem certeza que deseja excluir este cliente?')) {
            return false;
        }

        // Faz a requisição AJAX para excluir o cliente
        $.ajax({
            url: 'clientes.php', // Onde o servidor vai processar a requisição
            type: 'POST',
            data: {
                action: 'excluirCliente',
                clienteId: clienteId
            },
            success: function(response) {
                // Se a exclusão foi bem-sucedida, atualize a lista de clientes ou faça o que for necessário
                alert('Cliente excluído com sucesso!');
                // Recarrega a página para atualizar a lista de clientes
                location.reload();
            },
            error: function(xhr, status, error) {
                // Se houver erro na requisição AJAX
                alert('Erro ao excluir o cliente.');
                console.error(xhr.responseText);
            }
        });
    });

    $('#clienteInfo').hide();

    $('#searchCliente').click(function() {
        var cpf = $('#clienteCpf').val();

        $.ajax({
            url: 'buscar_cliente.php',
            type: 'POST',
            data: {cpf: cpf},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#clienteNome').val(response.nome);
                    $('#clienteEndereco').val(response.endereco);
                    $('#clienteInfo').show();
                } else {
                    $('#clienteNome').val('');
                    $('#clienteEndereco').val('');
                    $('#clienteInfo').show();
                }
            },
            error: function() {
                console.log('Erro ao buscar cliente');
            }
        });
    });

    $('#clienteCpf').on('input', function() {
        // Remove caracteres não numéricos
        var sanitized = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(sanitized);
    });

    $('#addProduto').click(function() {
        var newProdutoSelect = $('select[name="produtos[]"]:first').clone();
        newProdutoSelect.val('');
        $('#produtosContainer').append(newProdutoSelect);
    });

    //Funcoes para ORDEM ##########

    // Captura o evento de clique no botão de exclusão
    $('.delete-ordem').click(function(e) {
        e.preventDefault(); // Evita o comportamento padrão de seguir o link

        // Obtém o ID da ordem a ser excluída do atributo data-ordem-id
        var ordemId = $(this).data('ordem-id');
        console.log('ID da ordem a ser excluída:', ordemId); // Log para depuração

        // Confirmação antes de excluir (opcional)
        if (!confirm('Tem certeza que deseja excluir esta ordem?')) {
            return false;
        }

        // Faz a requisição AJAX para excluir a ordem
        $.ajax({
            url: 'ordem.php', // Onde o servidor vai processar a requisição
            type: 'POST',
            data: {
                action: 'excluirOrdem',
                ordemId: ordemId
            },
            success: function(response) {
                // Se a exclusão foi bem-sucedida, atualize a lista de ordens ou faça o que for necessário
                alert('Ordem excluída com sucesso!');
                console.log('Resposta do servidor:', response); // Log para depuração
                // Recarrega a página para atualizar a lista de ordens
                location.reload();
            },
            error: function(xhr, status, error) {
                // Se houver erro na requisição AJAX
                alert('Erro ao excluir a ordem.');
                console.error(xhr.responseText);
            }
        });
    });

    // Funções para os PRODUTOS ##############

    // Captura o evento de clique no botão de exclusão
    $('.delete-product').click(function(e) {
        e.preventDefault(); // Evita o comportamento padrão de seguir o link

        // Obtém o ID do produto a ser excluído do atributo data-product-id
        var produtoId = $(this).data('product-id');

        // Confirmação antes de excluir (opcional)
        if (!confirm('Tem certeza que deseja excluir este produto?')) {
            return false;
        }

        // Faz a requisição AJAX para excluir o produto
        $.ajax({
            url: 'produtos.php', // Onde o servidor vai processar a requisição
            type: 'POST',
            data: {
                action: 'excluirProduto',
                produtoId: produtoId
            },
            success: function(response) {
                // Se a exclusão foi bem-sucedida, atualize a lista de prosutos ou faça o que for necessário
                alert('Produto excluído com sucesso!');
                // Recarrega a página para atualizar a lista de produtos
                location.reload();
            },
            error: function(xhr, status, error) {
                // Se houver erro na requisição AJAX
                alert('Erro ao excluir o produto.');
                console.error(xhr.responseText);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var cpfInput = document.getElementById('cpf');

    cpfInput.addEventListener('input', function() {
        // Remove caracteres não numéricos
        this.value = this.value.replace(/\D/g, '');

        // Limita o campo a 11 caracteres
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });
});