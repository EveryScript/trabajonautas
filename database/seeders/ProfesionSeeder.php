<?php

namespace Database\Seeders;

use App\Models\Profesion;
use Illuminate\Database\Seeder;

class ProfesionSeeder extends Seeder
{
    public $now;

    public function run(): void
    {
        // Área Económica, Administrativa y Financiera
        Profesion::create([
            'id' => 1,
            'profesion_name' => 'Administración de Empresas',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 2,
            'profesion_name' => 'Contaduría Publica',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 3,
            'profesion_name' => 'Contaduría General',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 4,
            'profesion_name' => 'Economía',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 5,
            'profesion_name' => 'Auditoria',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 6,
            'profesion_name' => 'Ingeniería Comercial',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 7,
            'profesion_name' => 'Ingeniería Financiera',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 8,
            'profesion_name' => 'Ing. Financiera y de Riesgos',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 9,
            'profesion_name' => 'Contabilidad y Finanzas',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 10,
            'profesion_name' => 'ingeniería Empresarial',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 11,
            'profesion_name' => 'Marketing y Medios Digitales',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 12,
            'profesion_name' => 'Marketing y Publicidad',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 13,
            'profesion_name' => 'Negocios y Ciencia de Datos',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 14,
            'profesion_name' => 'Negocios y Tecnologías de Información',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 15,
            'profesion_name' => 'Ingeniería en Innovación Empresarial',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 16,
            'profesion_name' => 'Negocios y Diseño',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 17,
            'profesion_name' => 'Negocios Internacionales',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 18,
            'profesion_name' => 'Relaciones Internacionales',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 19,
            'profesion_name' => 'Creación y Desarrollo de Empresas',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 20,
            'profesion_name' => 'Economía e Inteligencia de Negocios',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 21,
            'profesion_name' => 'Comercio Internacional',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 22,
            'profesion_name' => 'Comercio Exterior',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 23,
            'profesion_name' => 'Gerencia y Administración Publica',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 24,
            'profesion_name' => 'Mercadotecnia',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 25,
            'profesion_name' => 'Comercio Internacional y Administración Aduanera',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 26,
            'profesion_name' => 'Ingeniería En Comercio Internacional',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 27,
            'profesion_name' => 'Ingeniería En Ciencia de Datos e Inteligencia de Negocios',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 28,
            'profesion_name' => 'Información y Control de Gestión',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 29,
            'profesion_name' => 'Administración Secretarial y Gestión Documental',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 30,
            'profesion_name' => 'Administración Turística',
            'area_id' => 1
        ]);
        Profesion::create([
            'id' => 31,
            'profesion_name' => 'Estadística',
            'area_id' => 1
        ]);

        // Área Legal
        Profesion::create([
            'id' => 32,
            'profesion_name' => 'Derecho',
            'area_id' => 2
        ]);
        Profesion::create([
            'id' => 33,
            'profesion_name' => 'Ciencias Políticas y Gestión Publica',
            'area_id' => 2
        ]);
        Profesion::create([
            'id' => 34,
            'profesion_name' => 'Ciencias Politicas y Relaciones Internacionales',
            'area_id' => 2
        ]);
        Profesion::create([
            'id' => 35,
            'profesion_name' => 'Derecho y Ciencias Juridicas',
            'area_id' => 2
        ]);
        Profesion::create([
            'id' => 36,
            'profesion_name' => 'Ciencias Juridicas',
            'area_id' => 2
        ]);
        Profesion::create([
            'id' => 37,
            'profesion_name' => 'Ciencias Politicas',
            'area_id' => 2
        ]);
        Profesion::create([
            'id' => 38,
            'profesion_name' => 'Ciencia Política y Administración Pública',
            'area_id' => 2
        ]);

        // Área Social
        Profesion::create([
            'id' => 39,
            'profesion_name' => 'Antropología y Arqueología',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 40,
            'profesion_name' => 'Antropología',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 41,
            'profesion_name' => 'Ciencias de la Comunicación Social',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 42,
            'profesion_name' => 'Sociología',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 43,
            'profesion_name' => 'Trabajo Social',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 44,
            'profesion_name' => 'Ciencias de la Información, Archivología - Bibliotecología - Documentación - Museología',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 45,
            'profesion_name' => 'Ciencias de la Educación',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 46,
            'profesion_name' => 'Filosofía',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 47,
            'profesion_name' => 'Filosofía y Letras',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 48,
            'profesion_name' => 'Historia',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 49,
            'profesion_name' => 'Lingüística e Idiomas',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 50,
            'profesion_name' => 'Lingüística Aplicada a la Enseñanza de Lenguas',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 51,
            'profesion_name' => 'Educación Intercultural Bilingüe',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 52,
            'profesion_name' => 'Literatura',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 53,
            'profesion_name' => 'Psicología',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 54,
            'profesion_name' => 'Psicopedagogia',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 55,
            'profesion_name' => 'Pedagogia',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 56,
            'profesion_name' => 'Turismo',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 57,
            'profesion_name' => 'Turismo y Hoteleria',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 58,
            'profesion_name' => 'Comunicación y Medios Digitales',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 59,
            'profesion_name' => 'Comunicación Digital Multimedia',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 60,
            'profesion_name' => 'Gestión del Turismo',
            'area_id' => 3
        ]);
        Profesion::create([
            'id' => 61,
            'profesion_name' => 'Lenguas Modernas y Filología Hispánica',
            'area_id' => 3
        ]);

        // Área Salud
        Profesion::create([
            'id' => 62,
            'profesion_name' => 'Bioquímica',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 63,
            'profesion_name' => 'Bioquímica y Farmacia',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 64,
            'profesion_name' => 'Medicina',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 65,
            'profesion_name' => 'Medicina General',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 66,
            'profesion_name' => 'Atencion Temprana y Educacion',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 67,
            'profesion_name' => 'Enfermería',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 68,
            'profesion_name' => 'Auxiliar de Enfermeria',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 69,
            'profesion_name' => 'Tecnico Medio en Enfermeria',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 70,
            'profesion_name' => 'Tecnico Medio en Enfermería Comunitaria',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 71,
            'profesion_name' => ' Enfermería Clínico-Quirúrgica',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 72,
            'profesion_name' => 'Enfermeria Obstetriz',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 73,
            'profesion_name' => 'Enfermeria Oncologica',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 74,
            'profesion_name' => 'Nutrición y Dietética',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 75,
            'profesion_name' => 'Nutrición Clínica y Dietética',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 76,
            'profesion_name' => 'Fisioterapia y Kinesiología',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 77,
            'profesion_name' => 'Tecnología Médica',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 78,
            'profesion_name' => 'Odontología',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 79,
            'profesion_name' => 'Ingenieria Biomédica',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 80,
            'profesion_name' => 'Ingeniería Bioquímica y de Bioprocesos',
            'area_id' => 4
        ]);
        Profesion::create([
            'id' => 81,
            'profesion_name' => 'Tecnico en Estadistica de Salud',
            'area_id' => 4
        ]);

        // Área Ingeniería
        Profesion::create([
            'id' => 82,
            'profesion_name' => 'Ingeniería Geográfica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 83,
            'profesion_name' => 'Ingeniería Geológica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 84,
            'profesion_name' => 'Ingeniería Ambiental',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 85,
            'profesion_name' => 'Ingenieria del Medio Ambiente',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 86,
            'profesion_name' => 'Ingenieria en Gestion Ambiental',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 87,
            'profesion_name' => 'Ingeniería de Alimentos',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 88,
            'profesion_name' => 'Ingeniería Civil',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 89,
            'profesion_name' => 'Ingeniería Eléctrica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 90,
            'profesion_name' => 'Ingenieria Agroindustrial',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 91,
            'profesion_name' => 'Ingeniería Industrial',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 92,
            'profesion_name' => 'Ingenieria en Industrias Alimentarias',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 93,
            'profesion_name' => 'Ingenieria Industrial y de Sistemas',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 94,
            'profesion_name' => 'Ingenieria en Desarrollo Rural',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 95,
            'profesion_name' => 'Ingeniería Mecánica y Electromecánica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 96,
            'profesion_name' => 'Ingeniería Agronómica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 97,
            'profesion_name' => 'Ingeniería en Producción y Comercialización Agropecuaria',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 98,
            'profesion_name' => 'Ingeniería Electrónica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 99,
            'profesion_name' => 'Ingenieria en Electrónica y Telecomunicaciones',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 100,
            'profesion_name' => 'Ingenieria en Electromecánica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 101,
            'profesion_name' => 'Ingenieria Aeronáutica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 102,
            'profesion_name' => 'Ingenieria en Telecomunicaciones',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 103,
            'profesion_name' => 'Ingenieria en Redes y Telecomunicaciones',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 104,
            'profesion_name' => 'Ingenieria Mecatrónica ',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 105,
            'profesion_name' => 'Ingeniería Metalúrgica y Materiales',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 106,
            'profesion_name' => 'Ingeniería Metalúrgica ',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 107,
            'profesion_name' => 'Ingeniería Petrolera',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 108,
            'profesion_name' => 'Ingenieria en Gestion Petrolera',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 109,
            'profesion_name' => 'Ingenieria Petroquimica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 110,
            'profesion_name' => 'Ingenieria en Petróleo, Gas y Energías',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 111,
            'profesion_name' => 'Ingeniería Química',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 112,
            'profesion_name' => 'Arquitectura',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 113,
            'profesion_name' => 'Arquitectura y Urbanismo',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 114,
            'profesion_name' => 'Planificación del Territorio y el Medio Ambiente',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 115,
            'profesion_name' => 'Ingenieria en Diseño y Programacion Digital',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 116,
            'profesion_name' => 'Construcciones Civiles',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 117,
            'profesion_name' => 'Ingenieria en Godesia y Topografia',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 118,
            'profesion_name' => 'Ingenieria en Geodesia, Topografía y Geomática',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 119,
            'profesion_name' => 'Ingenieria Minera',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 120,
            'profesion_name' => 'Ingenieria de Sistemas',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 121,
            'profesion_name' => 'Ingenieria en Informatica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 122,
            'profesion_name' => 'Ingeniería de Sistemas E Informatica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 123,
            'profesion_name' => 'Ingenieria en Sistemas Informáticos',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 124,
            'profesion_name' => 'Ingeniería de Sistemas de Computacion Administrativa',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 125,
            'profesion_name' => 'Tecnico Medio en Informatica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 126,
            'profesion_name' => 'Tecnico Medio en Redes y Sistemas de Comunicacion',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 127,
            'profesion_name' => 'Ciencias Fisicas y Energías Alternativas',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 128,
            'profesion_name' => 'Ingeniería en Producción Empresarial',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 129,
            'profesion_name' => 'Ingenieria en Desarrollo Tecnologico Productivo ',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 130,
            'profesion_name' => 'Ingeniería en Zootecnia e Industria Pecuaria',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 131,
            'profesion_name' => 'Ingeniería de Gas y Petroquímica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 132,
            'profesion_name' => 'Ingenieria en Robotica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 133,
            'profesion_name' => 'Ingeniería en Logística de Cadenas de Suministro',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 134,
            'profesion_name' => 'Ingeniería en Multimedia e Interactividad Digital',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 135,
            'profesion_name' => 'Tecnico Superior en Informatica',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 136,
            'profesion_name' => 'Tecnico Superior en Sistemas Electronicos',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 137,
            'profesion_name' => 'Ingenieria en Sistemas Electronicos',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 138,
            'profesion_name' => 'Tecnico Superior en Energias Renovables',
            'area_id' => 5
        ]);
        Profesion::create([
            'id' => 139,
            'profesion_name' => 'Tecnico Superior en Construccion Civil',
            'area_id' => 5
        ]);

        // Áreas Poco Frecuentes
        Profesion::create([
            'id' => 140,
            'profesion_name' => 'Veterinaria y Zootecnia',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 141,
            'profesion_name' => 'Biologia',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 142,
            'profesion_name' => 'Ciencias Químicas',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 143,
            'profesion_name' => 'Física',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 144,
            'profesion_name' => 'Matemáticas',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 145,
            'profesion_name' => 'Química Industrial',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 146,
            'profesion_name' => 'Artes Musicales',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 147,
            'profesion_name' => 'Artes Plasticas',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 148,
            'profesion_name' => 'Ingenieria Mecanica',
            'area_id' => 6
        ]);
        Profesion::create([
            'id' => 149,
            'profesion_name' => 'Ingenieria Automotriz',
            'area_id' => 6
        ]);
    }
}
