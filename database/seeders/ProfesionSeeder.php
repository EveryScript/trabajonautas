<?php

namespace Database\Seeders;

use App\Models\Profesion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfesionSeeder extends Seeder
{
    public $admin_id;

    public function run(): void
    {
        $profesions_data = [
            [1, 'Administración de Empresas', 1, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [2, 'Contaduría', 1, '2026-04-21 15:10:16', '2026-04-27 15:37:50'],
            [4, 'Economía', 1, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [5, 'Auditoria', 1, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [6, 'Ingeniería Comercial', 1, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [7, 'Ingeniería Financiera', 1, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [12, 'Marketing y Publicidad', 14, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [13, 'Negocios y Ciencia de Datos', 1, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [25, 'Comercio Internacional y Administración Aduanera o Negocios Internacionales', 15, '2026-04-21 15:10:16', '2026-04-27 15:55:27'],
            [29, 'Administración Secretarial y Gestión Documental', 35, '2026-04-21 15:10:16', '2026-04-27 15:58:35'],
            [31, 'Estadística', 4, '2026-04-21 15:10:16', '2026-04-27 15:29:27'],
            [35, 'Derecho y Ciencias Juridicas', 22, '2026-04-21 15:10:16', '2026-04-27 11:47:14'],
            [42, 'Sociología', 3, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [43, 'Trabajo Social', 3, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [44, 'Archivología, Bibliotecología, Documentación y Museología', 6, '2026-04-21 15:10:16', '2026-04-27 16:03:50'],
            [49, 'Lingüística e Idiomas', 25, '2026-04-21 15:10:16', '2026-04-27 12:44:43'],
            [53, 'Psicología', 28, '2026-04-21 15:10:16', '2026-04-27 15:02:15'],
            [54, 'Psicopedagogía', 28, '2026-04-21 15:10:16', '2026-04-28 09:43:36'],
            [57, 'Turismo y Hotelería', 17, '2026-04-21 15:10:16', '2026-04-28 09:44:04'],
            [58, 'Comunicación y Medios Digitales', 18, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [63, 'Bioquímica y Farmacia', 30, '2026-04-21 15:10:16', '2026-04-27 15:16:53'],
            [64, 'Medicina General', 29, '2026-04-21 15:10:16', '2026-04-27 16:39:43'],
            [74, 'Nutrición y Dietética', 31, '2026-04-21 15:10:16', '2026-04-27 15:18:18'],
            [76, 'Fisioterapia y Kinesiología', 32, '2026-04-21 15:10:16', '2026-04-27 15:22:10'],
            [78, 'Odontología', 33, '2026-04-21 15:10:16', '2026-04-27 15:23:40'],
            [79, 'Biomédica', 34, '2026-04-21 15:10:16', '2026-04-27 15:27:43'],
            [81, 'Estadística de Salud', 4, '2026-04-21 15:10:16', '2026-04-27 15:29:38'],
            [84, 'Ingeniería Ambiental', 41, '2026-04-21 15:10:16', '2026-04-27 16:34:07'],
            [87, 'Ingeniería de Alimentos', 37, '2026-04-21 15:10:16', '2026-04-27 16:17:46'],
            [89, 'Eléctrico/a', 16, '2026-04-21 15:10:16', '2026-04-27 12:30:44'],
            [90, 'Ingeniería Agroindustrial', 37, '2026-04-21 15:10:16', '2026-04-28 09:46:14'],
            [91, 'Ingeniería Industrial', 5, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [94, 'Ingeniería en Desarrollo Rural', 37, '2026-04-21 15:10:16', '2026-04-27 16:18:56'],
            [96, 'Ingeniería Agronómica', 37, '2026-04-21 15:10:16', '2026-04-27 16:16:45'],
            [101, 'Aeronáutica', 6, '2026-04-21 15:10:16', '2026-04-27 16:12:21'],
            [105, 'Metalúrgica y Materiales', 42, '2026-04-21 15:10:16', '2026-04-27 16:43:51'],
            [113, 'Arquitectura y Urbanismo', 13, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [118, 'Ingeniería en Geodesia, Topografía y Geomática', 39, '2026-04-21 15:10:16', '2026-04-28 09:50:17'],
            [119, 'Ingeniería Minera', 38, '2026-04-21 15:10:16', '2026-04-28 09:50:41'],
            [140, 'Veterinaria y Zootecnia', 36, '2026-04-21 15:10:16', '2026-04-27 16:05:23'],
            [141, 'Biología', 6, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [143, 'Física', 6, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [144, 'Matemáticas', 6, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [146, 'Artes Musicales', 6, '2026-04-21 15:10:16', '2026-04-21 15:10:16'],
            [147, 'Artes Plásticas', 6, '2026-04-21 15:10:16', '2026-04-28 09:51:41'],
            [150, 'Diseño Grafico', 20, '2026-04-21 15:10:16', '2026-04-25 11:49:18'],
            [152, 'Enfermería', 19, '2026-04-23 17:34:47', '2026-04-25 11:20:41'],
            [153, 'Sistemas e Informática', 9, '2026-04-23 17:38:00', '2026-04-23 17:38:00'],
            [154, 'Redes y Telecomunicaciones', 10, '2026-04-27 11:12:59', '2026-04-27 11:12:59'],
            [155, 'Química', 7, '2026-04-27 11:28:47', '2026-04-27 11:28:47'],
            [156, 'Petróleo, Gas y Energías', 21, '2026-04-27 11:31:08', '2026-04-27 11:35:48'],
            [157, 'Relaciones Internacionales, Ciencias Políticas y Gestión Pública', 2, '2026-04-27 11:55:06', '2026-04-27 11:55:06'],
            [158, 'Electrónica, Electromecánica y Mecatrónica', 11, '2026-04-27 12:04:08', '2026-04-28 09:57:34'],
            [159, 'Mecánica Automotriz', 23, '2026-04-27 12:09:54', '2026-04-27 12:12:21'],
            [160, 'Mecánica Industrial', 24, '2026-04-27 12:16:43', '2026-04-27 12:16:43'],
            [161, 'Civil y Construcciones Civiles', 12, '2026-04-27 12:21:27', '2026-04-27 12:21:27'],
            [162, 'Antropología, Historia y Filosofía', 26, '2026-04-27 12:55:28', '2026-04-27 12:55:28'],
            [163, 'Ciencias de la Educación y Pedagogía', 27, '2026-04-27 13:00:15', '2026-04-27 13:00:15'],
            [164, 'Secretariado Ejecutivo', 35, '2026-04-27 15:59:11', '2026-04-27 15:59:11'],
            [165, 'Ingeniería Geográfica', 40, '2026-04-27 16:32:05', '2026-04-27 16:32:05'],
            [166, 'Ingeniería Geológica', 40, '2026-04-27 16:32:20', '2026-04-27 16:32:20'],
            [167, 'Anatomía Patológica (Especialidad)', 43, '2026-04-28 10:01:18', '2026-04-28 10:01:18'],
            [168, 'Anestesiología (Especialidad)', 43, '2026-04-28 10:02:35', '2026-04-28 10:02:35'],
            [169, 'Cirugía General (Especialidad)', 43, '2026-04-28 10:04:06', '2026-04-28 10:04:06'],
            [170, 'Cirugía Oncológica (Especialidad)', 43, '2026-04-28 10:04:52', '2026-04-28 10:04:52'],
            [171, 'Cirugía Pediátrica (Especialidad)', 43, '2026-04-28 10:05:34', '2026-04-28 10:05:34'],
            [172, 'Cirugía Vascular (Especialidad)', 43, '2026-04-28 10:05:59', '2026-04-28 10:05:59'],
            [173, 'Coloproctología (Especialidad)', 43, '2026-04-28 10:07:07', '2026-04-28 10:07:07'],
            [174, 'Dermatología (Especialidad)', 43, '2026-04-28 10:07:44', '2026-04-28 10:07:44'],
            [175, 'Emergenciología (Especialidad)', 43, '2026-04-28 10:08:29', '2026-04-28 10:08:29'],
            [176, 'Gastroenterología Clínica (Especialidad)', 43, '2026-04-28 10:09:23', '2026-04-28 10:09:23'],
            [177, 'Endocrinología (Especialidad)', 43, '2026-04-28 10:09:52', '2026-04-28 10:09:52'],
            [178, 'Ginecología y Obstetricia (Especialidad)', 43, '2026-04-28 10:11:02', '2026-04-28 10:11:02'],
            [179, 'Imagenología (Especialidad) ', 43, '2026-04-28 10:12:12', '2026-04-28 10:12:12'],
            [180, 'Medicina Crítica y Terapia Intensiva (Especialidad)', 43, '2026-04-28 10:13:57', '2026-04-28 10:13:57'],
            [181, 'Medicina del Trabajo (Especialidad)', 43, '2026-04-28 10:14:35', '2026-04-28 10:14:35'],
            [182, 'Medicina Familiar (Especialidad)', 43, '2026-04-28 10:15:02', '2026-04-28 10:15:02'],
            [183, 'Medicina Interna (Especialidad)', 43, '2026-04-28 10:15:20', '2026-04-28 10:15:20'],
            [184, 'Nefrología (Especialidad)', 43, '2026-04-28 10:15:55', '2026-04-28 10:15:55'],
            [185, 'Neonatología (Especialidad)', 43, '2026-04-28 10:16:22', '2026-04-28 10:16:22'],
            [186, 'Neurocirugía (Especialidad)', 43, '2026-04-28 10:17:20', '2026-04-28 10:17:20'],
            [187, 'Neurología (Especialidad)', 43, '2026-04-28 10:17:47', '2026-04-28 10:17:47'],
            [188, 'Neurología Pediátrica (Especialidad)', 43, '2026-04-28 10:18:14', '2026-04-28 10:18:14'],
            [189, 'Oftalmología (Especialidad)', 43, '2026-04-28 10:18:49', '2026-04-28 10:18:49'],
            [190, 'Oncología Clínica (Especialidad)', 43, '2026-04-28 10:19:46', '2026-04-28 10:19:46'],
            [191, 'Oncología Ginecológica (Especialidad)', 43, '2026-04-28 10:20:31', '2026-04-28 10:20:31'],
            [192, 'Pediatría (Especialidad)', 43, '2026-04-28 10:20:55', '2026-04-28 10:20:55'],
            [193, 'Psiquiatría (Especialidad)', 43, '2026-04-28 10:21:34', '2026-04-28 10:21:34'],
            [194, 'Radio Oncología (Especialidad)', 43, '2026-04-28 10:22:05', '2026-04-28 10:22:05'],
            [195, 'Reumatología (Especialidad)', 43, '2026-04-28 10:22:32', '2026-04-28 10:22:32'],
            [196, 'Salud Familiar Comunitaria Intercultural SAFCI (Especialidad)', 43, '2026-04-28 10:23:09', '2026-04-28 10:23:09'],
            [197, 'Terapia Intensiva Pediátrica (Especialidad)', 43, '2026-04-28 10:23:35', '2026-04-28 10:23:35'],
            [198, 'Traumatología y Ortopedia (Especialidad)', 43, '2026-04-28 10:24:57', '2026-04-28 10:25:58'],
            [199, 'Urología (Especialidad)', 43, '2026-04-28 10:26:56', '2026-04-28 10:26:56'],
            [200, 'Anestesiología Pediátrica (Especialidad)', 43, '2026-04-28 10:31:27', '2026-04-28 10:31:27'],
            [201, 'Angiología y Cirugía Vascular (Especialidad)', 43, '2026-04-28 10:32:42', '2026-04-28 10:32:42'],
            [202, 'Cardiología Pediátrica (Especialidad)', 43, '2026-04-28 10:33:32', '2026-04-28 10:33:32'],
            [203, 'Cirugía Bucomaxilofacial (Especialidad)', 43, '2026-04-28 10:34:19', '2026-04-28 10:34:19'],
            [204, 'Geriatría y Gerontología (Especialidad)', 43, '2026-04-28 10:35:32', '2026-04-28 10:35:32'],
            [205, 'Hematología y Medicina Transfusional (Especialidad)', 43, '2026-04-28 10:37:48', '2026-04-28 10:37:48'],
            [206, 'Infectología Pediátrica (Especialidad)', 43, '2026-04-28 10:38:44', '2026-04-28 10:38:44'],
            [207, 'Medicina del Dolor (Especialidad)', 43, '2026-04-28 10:39:22', '2026-04-28 10:39:22'],
            [208, 'Medicina Física y Rehabilitación (Especialidad)', 43, '2026-04-28 10:40:22', '2026-04-28 10:40:22'],
            [209, 'Oncología Pediátrica (Especialidad)', 43, '2026-04-28 10:41:57', '2026-04-28 10:41:57'],
            [210, 'Ortopedia Pediátrica (Especialidad)', 43, '2026-04-28 10:42:46', '2026-04-28 10:42:46'],
            [211, 'Otorrinolaringología (Especialidad)', 43, '2026-04-28 10:44:09', '2026-04-28 10:44:09'],
            [212, 'Nefrología Pediátrica (Especialidad)', 43, '2026-04-28 10:47:19', '2026-04-28 10:47:19'],
            [213, 'Endoscopia Digestiva Avanzada (Especialidad)', 43, '2026-04-28 10:49:41', '2026-04-28 10:49:41'],
            [214, 'Infectología (Especialidad)', 43, '2026-04-28 10:50:31', '2026-04-28 10:50:31'],
            [215, 'Medicina Materno Fetal (Especialidad)', 43, '2026-04-28 10:51:12', '2026-04-28 10:51:12'],
            [216, 'Chofer Profesional', 44, '2026-04-28 10:54:39', '2026-04-28 10:54:39'],
            [217, 'Limpieza y Lavandería o Mucama (Manuales)', 45, '2026-04-28 10:55:42', '2026-04-28 11:19:47'],
            [218, 'Operador de Maquinaria Pesada', 46, '2026-04-28 11:03:32', '2026-04-28 11:03:32'],
            [219, 'Portero/a', 47, '2026-04-28 11:06:55', '2026-04-28 11:06:55'],
            [220, 'Gastronomía', 48, '2026-04-28 11:09:32', '2026-04-28 11:09:32'],
            [221, 'Cocinero/a', 48, '2026-04-28 11:09:59', '2026-04-28 11:09:59'],
            [222, 'Jardinero/a', 49, '2026-04-28 11:12:26', '2026-04-28 11:12:26'],
            [223, 'Seguridad Física e Institucional', 50, '2026-04-28 11:14:52', '2026-04-28 11:14:52'],
            [224, 'Mesero/a', 48, '2026-04-28 11:18:25', '2026-04-28 11:18:25'],
            [225, 'Plomero', 51, '2026-04-28 11:20:55', '2026-04-28 11:23:24'],
            [226, 'Albañil', 51, '2026-04-28 11:22:03', '2026-04-28 11:22:03'],
            [227, 'Electricista', 51, '2026-04-28 11:22:44', '2026-04-28 11:22:44'],
            [228, 'Ingeniería Forestal', 41, '2026-04-28 11:58:49', '2026-04-28 11:58:49'],
        ];

        $this->admin_id = User::where('email', 'ricardooropeza15@gmail.com')->first()->id;
        if (!$this->admin_id) return;

        $profesions = array_map(function ($profesion) {
            DB::table('area_profesion')->insert([
                'profesion_id' => $profesion[0],
                'area_id' => $profesion[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'id' => $profesion[0],
                'profesion_name' => $profesion[1],
                'user_id' => $this->admin_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $profesions_data);



        Profesion::insert($profesions);
    }
}
