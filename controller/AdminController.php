<?php

class AdminController extends BaseController
{

    private function necesitaAdmin(){
        $user = $this->getCurrentUser();
        $rol = $user['rol'] ?? ''; 
        if($rol != 'Administrador'){
            Redirect::to('/tpfinal_mvc/User/home');
            exit;
        }
    }

    public function estadisticas(){
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }
        $this->necesitaAdmin();

        $periodosValidos = ['dia', 'semana', 'mes', 'anio', 'todo'];
        $periodo = $_GET['periodo'] ?? 'todo';
        if (!in_array($periodo, $periodosValidos, true)) {
            $periodo = 'todo';
        }

        $stats = $this->model->getEstadisticas($periodo);
        $stats['user'] = $user;

        //Generar los gráficos con jpGraph
        require_once __DIR__ . '/../libs/jpgraph/src/jpgraph.php';
        require_once __DIR__ . '/../libs/jpgraph/src/jpgraph_bar.php';
        require_once __DIR__ . '/../libs/jpgraph/src/jpgraph_pie.php';

        $labels = [];
        $valores = [];
        foreach ($stats['aciertos_por_usuario'] as $fila) {
            $labels[] = $fila['usuario'];
            $valores[] = (float) $fila['porcentaje'];
        }
        $stats['grafico_aciertos'] = $this->generarGraficoBarras($valores, $labels, '% de aciertos por usuario');

        $labelsPais = [];
        $valoresPais = [];
        foreach ($stats['usuarios_por_pais'] as $fila) {
            $labelsPais[] = $fila['pais'];
            $valoresPais[] = (float) $fila['cantidad'];
        }
        $stats['grafico_pais'] = $this->generarGraficoTorta($valoresPais, $labelsPais, 'Usuarios por país');

        $valoresSexo = [];
        $labelsSexo = [];
        foreach ($stats['usuarios_por_sexo'] as $fila) {
            $labelsSexo[] = $fila['sexo'];
            $valoresSexo[] = (float) $fila['cantidad'];
        }
        $stats['grafico_sexo'] = $this->generarGraficoTorta($valoresSexo, $labelsSexo, 'Usuarios por sexo');

        $valoresEdad = [];
        $labelsEdad = [];
        foreach ($stats['usuarios_por_edad'] as $fila) {
            $labelsEdad[] = $fila['grupo'];
            $valoresEdad[] = (float) $fila['cantidad'];
        }
        $stats['grafico_edad'] = $this->generarGraficoTorta($valoresEdad, $labelsEdad, 'Usuarios por grupo de edad');

        $this->render('estadisticasView', $stats);
    }

    public function estadisticasPdf(){
        $this->ensureSession();

        $user = $this->getCurrentUser();
        if (!$user) {
            Redirect::to('/tpfinal_mvc/User/login');
            return;
        }

        $this->necesitaAdmin();

        $periodosValidos = ['dia', 'semana', 'mes', 'anio', 'todo'];
        $periodo = $_GET['periodo'] ?? 'todo';
        if (!in_array($periodo, $periodosValidos, true)) {
            $periodo = 'todo';
        }

        $stats = $this->model->getEstadisticas($periodo);

        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../libs/jpgraph/src/jpgraph.php';
        require_once __DIR__ . '/../libs/jpgraph/src/jpgraph_bar.php';
        require_once __DIR__ . '/../libs/jpgraph/src/jpgraph_pie.php';

        //Gráfico de barras: % aciertos por usuario
        $labels = [];
        $valores = [];
        foreach ($stats['aciertos_por_usuario'] as $fila) {
            $labels[] = $fila['usuario'];
            $valores[] = (float) $fila['porcentaje'];
        }
        $graficoAciertos = $this->generarGraficoBarras($valores, $labels, '% de aciertos por usuario');

        //Gráfico de torta: usuarios por pais
        $labelsPais = [];
        $valoresPais = [];
        foreach ($stats['usuarios_por_pais'] as $fila) {
            $labelsPais[] = $fila['pais'];
            $valoresPais[] = (float) $fila['cantidad'];
        }
        $graficoPais = $this->generarGraficoTorta($valoresPais, $labelsPais, 'Usuarios por país');

        //Gráfico de torta: usuarios por sexo
        $valoresSexo = [];
        $labelsSexo = [];
        foreach ($stats['usuarios_por_sexo'] as $fila) {
            $labelsSexo[] = $fila['sexo'];
            $valoresSexo[] = (float) $fila['cantidad'];
        }
        $graficoSexo = $this->generarGraficoTorta($valoresSexo, $labelsSexo, 'Usuarios por sexo');

        //Gráfico de torta: usuarios por grupo de edad
        $valoresEdad = [];
        $labelsEdad = [];
        foreach ($stats['usuarios_por_edad'] as $fila) {
            $labelsEdad[] = $fila['grupo'];
            $valoresEdad[] = (float) $fila['cantidad'];
        }
        $graficoEdad = $this->generarGraficoTorta($valoresEdad, $labelsEdad, 'Usuarios por grupo de edad');

        $html = $this->armarHtmlPdf($stats, $periodo, $graficoAciertos, $graficoPais, $graficoSexo, $graficoEdad);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('estadisticas_' . $periodo . '.pdf', ['Attachment' => false]);
        exit;
    }

    private function generarGraficoBarras(array $valores, array $labels, string $titulo): string {
        $graph = new \Graph(700, 400);
        $graph->SetScale('textlin');
        $graph->title->Set($titulo);
        $graph->SetMargin(60, 30, 40, 100);
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);

        $bplot = new \BarPlot($valores);
        $bplot->SetColor('white');
        $bplot->SetFillColor('#6a4c93');
        $bplot->value->Show();
        $graph->Add($bplot);

        ob_start();
        $graph->Stroke(_IMG_HANDLER);
        $img = $graph->img->img;
        ob_end_clean();

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    private function generarGraficoTorta(array $valores, array $labels, string $titulo): string {
        $graph = new \PieGraph(500, 350);
        $graph->title->Set($titulo);

        $pplot = new \PiePlot($valores);
        $pplot->SetLegends($labels);
        $pplot->SetColor('white');
        $pplot->SetSliceColors(['#6a4c93', '#1982c4', '#8ac926']);
        $graph->Add($pplot);

        ob_start();
        $graph->Stroke(_IMG_HANDLER);
        $img = $graph->img->img;
        ob_end_clean();

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    private function armarHtmlPdf(array $stats, string $periodo, string $graficoAciertos, string $graficoPais, string $graficoSexo, string $graficoEdad): string {
        ob_start();
        ?>
        <html>
        <head>
            <style>
                body { font-family: sans-serif; font-size: 12px; color: #222; }
                h1 { color: #6a4c93; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ccc; padding: 4px 8px; text-align: left; }
                .kpis { display: table; width: 100%; margin-bottom: 20px; }
                .kpi { display: table-cell; text-align: center; padding: 10px; }
                .kpi span { font-size: 20px; font-weight: bold; display: block; }
                img { max-width: 100%; }
            </style>
        </head>
        <body>
            <h1>Estadísticas (<?= htmlspecialchars($periodo) ?>)</h1>

            <div class="kpis">
                <div class="kpi"><span><?= $stats['cantidad_jugadores'] ?></span>Jugadores</div>
                <div class="kpi"><span><?= $stats['cantidad_partidas'] ?></span>Partidas</div>
                <div class="kpi"><span><?= $stats['cantidad_preguntas'] ?></span>Preguntas</div>
                <div class="kpi"><span><?= $stats['cantidad_preguntas_creadas'] ?></span>Sugeridas</div>
                <div class="kpi"><span><?= $stats['cantidad_usuarios_nuevos'] ?></span>Usuarios nuevos</div>
            </div>

            <img src="<?= $graficoAciertos ?>">

            <table>
                <tr><th>Usuario</th><th>Respondidas</th><th>Correctas</th><th>%</th></tr>
                <?php foreach ($stats['aciertos_por_usuario'] as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['usuario']) ?></td>
                    <td><?= $fila['preguntas_respondidas'] ?></td>
                    <td><?= $fila['preguntas_correctas'] ?></td>
                    <td><?= $fila['porcentaje'] ?>%</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <img src="<?= $graficoPais ?>">
            <table>
                <tr><th>País</th><th>Cantidad</th></tr>
                <?php foreach ($stats['usuarios_por_pais'] as $fila): ?>
                <tr><td><?= htmlspecialchars($fila['pais']) ?></td><td><?= $fila['cantidad'] ?></td></tr>
                <?php endforeach; ?>
            </table>

            <img src="<?= $graficoSexo ?>">
            <table>
                <tr><th>Género</th><th>Cantidad</th></tr>
                <?php foreach ($stats['usuarios_por_sexo'] as $fila): ?>
                <tr><td><?= htmlspecialchars($fila['sexo']) ?></td><td><?= $fila['cantidad'] ?></td></tr>
                <?php endforeach; ?>
            </table>

            <img src="<?= $graficoEdad ?>">
            <table>
                <tr><th>Grupo Etario</th><th>Cantidad</th></tr>
                <?php foreach ($stats['usuarios_por_edad'] as $fila): ?>
                <tr><td><?= htmlspecialchars($fila['grupo']) ?></td><td><?= $fila['cantidad'] ?></td></tr>
                <?php endforeach; ?>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

}