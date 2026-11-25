
<table>
    <tbody>
    <tr>
        <td>
            Data do Envio: <?= date('d/m/Y H:i:s') ?>
        </td>
    </tr>
    <tr>
        <td>Identificação: <?= $formulario['identificacao'] ?></td>
    </tr>
    <?php if ($formulario['ra'] != null) : ?>
        <tr>
            <td>
                RA: <?= $formulario['ra']  ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td>
            Nome: <?= $formulario['nome'] ?>
        </td>
    </tr>
    <tr>
        <td>
            Email: <?= $formulario['email'] ?>
        </td>
    </tr>
    <tr>
        <td>
            Telefone: <?= $formulario['telefone'] ?>
        </td>
    </tr>
    <tr>
        <td>
            Mensagem: <?= $formulario['mensagem'] ?>
        </td>
    </tr>
    </tbody>
</table>
