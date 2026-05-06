<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Profesion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public $admin_id;

    public function run(): void
    {

        $area_data = [
            [1, 'Área ECONÓMICA, ADMINISTRATIVA Y FINANCIERA', 'Gestión de recursos, análisis de mercados y optimización de procesos para alcanzar objetivos empresariales y maximizar la rentabilidad.', 'e0de2b68-777e-4e67-8443-464a0b3a4a60'],
            [2, 'Área RELACIONES INTERNACIONALES, POLÍTICA Y GESTION PÚBLICA ', 'Profesiones relacionadas con Relaciones Internacionales, Política y Gestión Pública', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [3, 'Área SOCIAL', 'Profesiones relacionadas al área SOCIAL', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [4, 'Área ESTADÍSTICA', 'Profesiones relacionadas a Estadística', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [5, 'Área INDUSTRIAL', 'Profesiones relacionadas a Ing. INDUSTRIAL', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [6, 'Áreas POCO FRECUENTES', 'Profesiones poco Frecuentes', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [7, 'Área QUÍMICA', 'profesiones relacionadas a QUÍMICA INDUSTRIAL', 'e0de2b68-777e-4e67-8443-464a0b3a4a60'],
            [9, 'Área SISTEMAS Y INFORMÁTICA', 'Profesiones relacionadas a SISTEMAS / INFORMÁTICA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [10, 'Área TELECOMUNICACIONES', 'Profesiones relacionadas a TELECOMUNICACIONES', 'e0de2b68-777e-4e67-8443-464a0b3a4a60'],
            [11, 'Área ELECTRÓNICA, ELECTROMECÁNICA y MECATRÓNICA', 'Profesiones relacionadas a ELECTRÓNICA, ELECTROMECÁNICA y MECATRÓNICA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [12, 'Área CIVIL Y CONSTRUCCIÓN', 'Profesiones relacionadas a CIVIL / CONSTRUCCIÓN', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [13, 'Área ARQUITECTURA Y URBANISMO', 'profesiones relacionadas a ARQUITECTURA / URBANISMO', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [14, 'Área MARKETING Y PUBLICIDAD', 'Profesiones relacionadas a MARKETING Y PUBLICIDAD', 'e0de2b68-777e-4e67-8443-464a0b3a4a60'],
            [15, 'Área COMERCIO INTERNACIONAL', 'Profesiones relacionadas a COMERCIO INTERNACIONAL', 'e0de2b68-777e-4e67-8443-464a0b3a4a60'],
            [16, 'Área ELECTRICA', 'Profesiones relacionadas a ELECTRICIDAD', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [17, 'Área TURISMO Y HOTELERIA', 'Profesiones relacionadas a TURISMO', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [18, 'Área COMUNICACIÓN Y MEDIOS DIGITALES', 'Profesiones relacionadas a COMUNICACIÓN Y MEDIOS DIGITALES', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [19, 'Área ENFERMERÍA', 'Profesiones relacionadas a Enfermería', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [20, 'Área DISEÑO', 'Profesiones Relacionadas con Diseño Gráfico', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [21, 'Área PETROLERA', 'Profesiones relacionadas a Petrolera', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [22, 'Área DERECHO', 'Profesiones relacionadas a Derecho', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [23, 'Área MECÁNICA AUTOMOTRIZ', 'Profesiones relacionadas a MECÁNICA AUTOMOTRIZ', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [24, 'Área MECÁNICA INDUSTRIAL', 'Profesiones relacionadas a MECÁNICA INDUSTRIAL', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [25, 'Área LINGUISTICA E IDIOMAS', 'Profesiones relacionadas a LINGUISTICA E IDIOMAS', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [26, 'Área ANTROPOLOGIA, HISTORIA Y FILOSOFIA', 'Profesiones relacionadas a ANTROPOLOGIA, HISTORIA Y FILOSOFIA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [27, 'Área EDUCACION', 'Profesiones relacionadas a EDUCACION', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [28, 'Área PSICOLOGÍA Y PSICOPEDAGOGÍA', 'Profesiones relacionadas a Psicología y Psicopedagogía', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [29, 'Área MEDICINA', 'Profesiones relacionadas al Área de MEDICINA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [30, 'Área BIOQUIMICA Y FARMACIA', 'Profesiones relacionadas a Bioquímica y Farmacia', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [31, 'Área NUTRICIÓN Y DIETÉTICA', 'Profesiones relacionadas a NUTRICIÓN Y DIETÉTICA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [32, 'Área FISIOTERAPIA Y KINESIOLOGÍA', 'Profesiones relacionadas con Fisioterapia y Kinesiología', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [33, 'Área ODONTOLOGÍA', 'Profesiones relacionadas a Odontología', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [34, 'Área BIOMEDICA', 'Profesiones relacionadas a BIOMEDICA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [35, 'Área SECRETARIADO', 'Profesiones relacionadas con Secretariado', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [36, 'Área VETERINARIA Y ZOOTECNIA', 'Profesiones relacionadas a VETERINARIA Y ZOOTECNIA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [37, 'Área AGRONÓMICA', 'Profesiones relacionadas a AGRONOMIA Y DESARROLLO RURAL', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [38, 'Área MINERA', 'Profesiones relacionadas a MINERIA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [39, 'Área GEODESIA, TOPOGRAFIA Y GEOMATICA', 'Profesiones relacionadas a GEODESIA, TOPOGRAFIA Y GEOMATICA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [40, 'Área GEOLOGICA y GEOGRAFICA', 'Profesiones relacionadas a ING. GEOLOGICA y GEOGRAFICA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [41, 'Área AMBIENTAL', 'Profesiones relacionadas al Medio Ambiente', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [42, 'Área METALURGICA Y MATERIALES', 'Profesiones relacionadas a METALURGICA Y MATERIALES', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [43, 'Área ESPECIALIDADES MEDICAS', 'Especialidades Médicas', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [44, 'Área CHOFERES', 'Choferes', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [45, 'Área LIMPIEZA', 'Limpieza', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [46, 'Área OPERADORES DE MAQUINARIA', 'Operadores de Maquinaria', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [47, 'Área PORTERIA', 'Portería', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [48, 'Área GASTRONOMÍA Y COCINA', 'Profesionales del área de GASTRONOMÍA Y COCINA', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [49, 'Área JARDINERÍA', 'Jardinería', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [50, 'Área SEGURIDAD', 'Seguridad Física ', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
            [51, 'Área MANTENIMIENTO', 'Profesionales de Mantenimiento', 'a19acc2e-2ea9-4a7c-ac4e-b1646b3a9ea6'],
        ];

        $this->admin_id = User::where('email', 'ricardooropeza15@gmail.com')->first()->id;
        if (!$this->admin_id) return;

        $areas = array_map(function ($area) {
            return [
                'id' => $area[0],
                'area_name' => $area[1],
                'description' => $area[2],
                'user_id' => $this->admin_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $area_data);

        Area::insert($areas);
    }
}
