<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnosa;
use App\Models\KeluhanMaster;
use App\Models\TindakanMaster;

class MedicalMasterSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // DIAGNOSA MASTER DATA
        // =========================================
        $diagnosas = [
            // Penyakit Infeksi & Demam
            ['kode' => 'A09', 'nama' => 'Diare & Gastroenteritis', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'A15', 'nama' => 'Tuberkulosis (TBC)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'A90', 'nama' => 'Demam Berdarah Dengue (DBD)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'B05', 'nama' => 'Campak (Morbili)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'B06', 'nama' => 'Rubella', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'B07', 'nama' => 'Cacar Air (Varicella)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'B26', 'nama' => 'Gondongan (Parotitis)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'J00', 'nama' => 'Flu / ISPA Ringan (Common Cold)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'J06', 'nama' => 'Infeksi Saluran Napas Atas (ISPA)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'J11', 'nama' => 'Influenza', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'R50', 'nama' => 'Febris (Demam)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'A77', 'nama' => 'Tifoid (Demam Tifus)', 'kategori' => 'Infeksi & Demam'],
            ['kode' => 'B34', 'nama' => 'Infeksi Virus Tidak Spesifik', 'kategori' => 'Infeksi & Demam'],

            // Gangguan Pencernaan
            ['kode' => 'K21', 'nama' => 'Gastritis / Maag', 'kategori' => 'Gangguan Pencernaan'],
            ['kode' => 'K29', 'nama' => 'Dyspepsia (Gangguan Lambung)', 'kategori' => 'Gangguan Pencernaan'],
            ['kode' => 'K59', 'nama' => 'Konstipasi (Sembelit)', 'kategori' => 'Gangguan Pencernaan'],
            ['kode' => 'K92', 'nama' => 'Mual Tanpa Sebab Jelas', 'kategori' => 'Gangguan Pencernaan'],
            ['kode' => 'R11', 'nama' => 'Muntah-muntah', 'kategori' => 'Gangguan Pencernaan'],
            ['kode' => 'K52', 'nama' => 'Kolitis / Radang Usus', 'kategori' => 'Gangguan Pencernaan'],
            ['kode' => 'B77', 'nama' => 'Cacingan (Ascariasis)', 'kategori' => 'Gangguan Pencernaan'],

            // Gangguan Pernapasan
            ['kode' => 'J45', 'nama' => 'Asma Bronkial', 'kategori' => 'Gangguan Pernapasan'],
            ['kode' => 'J18', 'nama' => 'Pneumonia', 'kategori' => 'Gangguan Pernapasan'],
            ['kode' => 'J20', 'nama' => 'Bronkitis Akut', 'kategori' => 'Gangguan Pernapasan'],
            ['kode' => 'J30', 'nama' => 'Rhinitis Alergi', 'kategori' => 'Gangguan Pernapasan'],
            ['kode' => 'J02', 'nama' => 'Faringitis Akut (Radang Tenggorokan)', 'kategori' => 'Gangguan Pernapasan'],
            ['kode' => 'J03', 'nama' => 'Tonsilitis (Amandel)', 'kategori' => 'Gangguan Pernapasan'],
            ['kode' => 'J40', 'nama' => 'Batuk Kronik', 'kategori' => 'Gangguan Pernapasan'],

            // Gangguan Kulit
            ['kode' => 'L20', 'nama' => 'Dermatitis Atopik (Eksim)', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'L30', 'nama' => 'Dermatitis Kontak', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'L50', 'nama' => 'Urtikaria (Biduran / Alergi Kulit)', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'B35', 'nama' => 'Tinea (Jamur Kulit)', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'B86', 'nama' => 'Skabies (Kudis)', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'L70', 'nama' => 'Acne (Jerawat)', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'L08', 'nama' => 'Impetigo (Infeksi Kulit Bakteri)', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'S01', 'nama' => 'Luka Terbuka / Laserasi', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'T14', 'nama' => 'Luka Bakar Ringan', 'kategori' => 'Gangguan Kulit'],
            ['kode' => 'L03', 'nama' => 'Selulitis (Infeksi Jaringan Bawah Kulit)', 'kategori' => 'Gangguan Kulit'],

            // Gangguan Mata & THT
            ['kode' => 'H10', 'nama' => 'Konjungtivitis (Sakit Mata / Merah)', 'kategori' => 'Mata & THT'],
            ['kode' => 'H66', 'nama' => 'Otitis Media (Infeksi Telinga Tengah)', 'kategori' => 'Mata & THT'],
            ['kode' => 'H92', 'nama' => 'Otalgia (Nyeri Telinga)', 'kategori' => 'Mata & THT'],
            ['kode' => 'J32', 'nama' => 'Sinusitis', 'kategori' => 'Mata & THT'],

            // Gangguan Muskuloskeletal
            ['kode' => 'M79', 'nama' => 'Myalgia (Nyeri Otot)', 'kategori' => 'Muskuloskeletal'],
            ['kode' => 'M25', 'nama' => 'Nyeri Sendi', 'kategori' => 'Muskuloskeletal'],
            ['kode' => 'S93', 'nama' => 'Keseleo / Sprain Pergelangan', 'kategori' => 'Muskuloskeletal'],
            ['kode' => 'S09', 'nama' => 'Cedera Kepala Ringan', 'kategori' => 'Muskuloskeletal'],
            ['kode' => 'S80', 'nama' => 'Memar / Kontusio', 'kategori' => 'Muskuloskeletal'],

            // Neurologi & Psikiatri
            ['kode' => 'G43', 'nama' => 'Migrain', 'kategori' => 'Neurologi & Psikiatri'],
            ['kode' => 'G44', 'nama' => 'Sakit Kepala (Cephalgia)', 'kategori' => 'Neurologi & Psikiatri'],
            ['kode' => 'F41', 'nama' => 'Gangguan Kecemasan (Ansietas)', 'kategori' => 'Neurologi & Psikiatri'],
            ['kode' => 'F32', 'nama' => 'Depresi Ringan', 'kategori' => 'Neurologi & Psikiatri'],
            ['kode' => 'G47', 'nama' => 'Gangguan Tidur (Insomnia)', 'kategori' => 'Neurologi & Psikiatri'],
            ['kode' => 'R55', 'nama' => 'Pingsan (Sinkop)', 'kategori' => 'Neurologi & Psikiatri'],
            ['kode' => 'R56', 'nama' => 'Kejang / Epilepsi', 'kategori' => 'Neurologi & Psikiatri'],

            // Urologi & Reproduksi
            ['kode' => 'N39', 'nama' => 'Infeksi Saluran Kemih (ISK)', 'kategori' => 'Urologi'],
            ['kode' => 'N23', 'nama' => 'Kolik Ginjal', 'kategori' => 'Urologi'],

            // Lainnya
            ['kode' => 'E11', 'nama' => 'Diabetes Melitus Tipe 2', 'kategori' => 'Metabolik'],
            ['kode' => 'I10', 'nama' => 'Hipertensi', 'kategori' => 'Kardiovaskular'],
            ['kode' => 'D50', 'nama' => 'Anemia', 'kategori' => 'Hematologi'],
            ['kode' => 'T78', 'nama' => 'Reaksi Alergi Akut', 'kategori' => 'Alergi & Imunologi'],
            ['kode' => 'Z00', 'nama' => 'Pemeriksaan Rutin / Medical Check-Up', 'kategori' => 'Umum'],
            ['kode' => 'R69', 'nama' => 'Kondisi Tidak Spesifik', 'kategori' => 'Umum'],
        ];

        foreach ($diagnosas as $d) {
            Diagnosa::firstOrCreate(['nama' => $d['nama']], $d);
        }

        // =========================================
        // KELUHAN MASTER DATA
        // =========================================
        $keluhanList = [
            // Kepala & Saraf
            ['nama' => 'Sakit kepala', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Pusing / Vertigo', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Migrain', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Kepala terasa berat', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Pingsan / Kehilangan Kesadaran', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Kejang', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Gelisah / Cemas berlebihan', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Sulit tidur (Insomnia)', 'kategori' => 'Kepala & Saraf'],
            ['nama' => 'Lemas / Tidak bertenaga', 'kategori' => 'Kepala & Saraf'],

            // Demam & Umum
            ['nama' => 'Demam tinggi (>38°C)', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Demam ringan (37-38°C)', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Menggigil', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Badan pegal-pegal', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Nafsu makan menurun', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Berat badan menurun', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Kelelahan ekstrem', 'kategori' => 'Demam & Umum'],
            ['nama' => 'Pucat / Anemia', 'kategori' => 'Demam & Umum'],

            // Pernapasan
            ['nama' => 'Batuk berdahak', 'kategori' => 'Pernapasan'],
            ['nama' => 'Batuk kering', 'kategori' => 'Pernapasan'],
            ['nama' => 'Batuk berdarah', 'kategori' => 'Pernapasan'],
            ['nama' => 'Sesak napas', 'kategori' => 'Pernapasan'],
            ['nama' => 'Napas berbunyi (Mengi / Wheezing)', 'kategori' => 'Pernapasan'],
            ['nama' => 'Hidung tersumbat', 'kategori' => 'Pernapasan'],
            ['nama' => 'Pilek / Ingus berlebihan', 'kategori' => 'Pernapasan'],
            ['nama' => 'Nyeri dada saat bernapas', 'kategori' => 'Pernapasan'],
            ['nama' => 'Bersin-bersin', 'kategori' => 'Pernapasan'],

            // Pencernaan
            ['nama' => 'Mual', 'kategori' => 'Pencernaan'],
            ['nama' => 'Muntah', 'kategori' => 'Pencernaan'],
            ['nama' => 'Nyeri ulu hati / Maag', 'kategori' => 'Pencernaan'],
            ['nama' => 'Nyeri perut bawah', 'kategori' => 'Pencernaan'],
            ['nama' => 'Kembung', 'kategori' => 'Pencernaan'],
            ['nama' => 'Diare (BAB cair >3x)', 'kategori' => 'Pencernaan'],
            ['nama' => 'Sembelit (Susah BAB)', 'kategori' => 'Pencernaan'],
            ['nama' => 'BAB berdarah', 'kategori' => 'Pencernaan'],
            ['nama' => 'Nyeri perut kanan bawah', 'kategori' => 'Pencernaan'],

            // Kulit
            ['nama' => 'Gatal-gatal seluruh badan', 'kategori' => 'Kulit'],
            ['nama' => 'Ruam / Bintik merah di kulit', 'kategori' => 'Kulit'],
            ['nama' => 'Luka terbuka / Lecet', 'kategori' => 'Kulit'],
            ['nama' => 'Bengkak pada kulit', 'kategori' => 'Kulit'],
            ['nama' => 'Bisul / Furunkel', 'kategori' => 'Kulit'],
            ['nama' => 'Kulit kuning (Ikterik)', 'kategori' => 'Kulit'],
            ['nama' => 'Bintik berair (Vesikel)', 'kategori' => 'Kulit'],

            // Mata & THT
            ['nama' => 'Mata merah dan berair', 'kategori' => 'Mata & THT'],
            ['nama' => 'Mata gatal', 'kategori' => 'Mata & THT'],
            ['nama' => 'Penglihatan kabur', 'kategori' => 'Mata & THT'],
            ['nama' => 'Nyeri telinga', 'kategori' => 'Mata & THT'],
            ['nama' => 'Telinga berdenging (Tinnitus)', 'kategori' => 'Mata & THT'],
            ['nama' => 'Nyeri tenggorokan', 'kategori' => 'Mata & THT'],
            ['nama' => 'Suara serak', 'kategori' => 'Mata & THT'],
            ['nama' => 'Gusi / Gigi sakit', 'kategori' => 'Mata & THT'],

            // Muskuloskeletal
            ['nama' => 'Nyeri sendi lutut', 'kategori' => 'Muskuloskeletal'],
            ['nama' => 'Nyeri punggung', 'kategori' => 'Muskuloskeletal'],
            ['nama' => 'Keseleo / Terkilir', 'kategori' => 'Muskuloskeletal'],
            ['nama' => 'Kram otot', 'kategori' => 'Muskuloskeletal'],
            ['nama' => 'Patah tulang (Fraktur)', 'kategori' => 'Muskuloskeletal'],
            ['nama' => 'Memar (Lebam)', 'kategori' => 'Muskuloskeletal'],
            ['nama' => 'Cedera akibat olahraga', 'kategori' => 'Muskuloskeletal'],

            // Urologi
            ['nama' => 'Nyeri saat buang air kecil', 'kategori' => 'Urologi'],
            ['nama' => 'Sering buang air kecil', 'kategori' => 'Urologi'],
            ['nama' => 'Urine berwarna keruh / berdarah', 'kategori' => 'Urologi'],
        ];

        foreach ($keluhanList as $k) {
            KeluhanMaster::firstOrCreate(['nama' => $k['nama']], $k);
        }

        // =========================================
        // TINDAKAN MASTER DATA
        // =========================================
        $tindakanList = [
            // Observasi & Pemantauan
            ['nama' => 'Pemantauan tanda vital (TTV)', 'kategori' => 'Observasi'],
            ['nama' => 'Observasi suhu tiap 4 jam', 'kategori' => 'Observasi'],
            ['nama' => 'Pemantauan saturasi oksigen', 'kategori' => 'Observasi'],
            ['nama' => 'Pemantauan tekanan darah berkala', 'kategori' => 'Observasi'],
            ['nama' => 'Observasi status kesadaran', 'kategori' => 'Observasi'],
            ['nama' => 'Istirahat penuh di UKS', 'kategori' => 'Observasi'],

            // Fisik & Terapi
            ['nama' => 'Kompres hangat', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Kompres dingin', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Bidai / Pembidaian', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Perban / Balutan luka', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Jahit luka (Hecting)', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Perawatan luka dan debridement', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Inhalasi / Nebulisasi', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Pemasangan infus', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Hidrasi oral (minum air putih banyak)', 'kategori' => 'Terapi Fisik'],
            ['nama' => 'Pijat / Terapi relaksasi', 'kategori' => 'Terapi Fisik'],

            // Farmakologi (Obat)
            ['nama' => 'Pemberian antipiretik (Parasetamol)', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian antasida / Maag', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian antibiotik oral', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian antihistamin', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian obat pereda nyeri (Analgesik)', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian obat antidiare (Loperamide)', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian obat batuk (Ekspektoran)', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian oralit / Rehidrasi', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian vitamin C & Multivitamin', 'kategori' => 'Farmakologi'],
            ['nama' => 'Pemberian salep/krim topikal', 'kategori' => 'Farmakologi'],
            ['nama' => 'Nebulisasi Salbutamol (Asma)', 'kategori' => 'Farmakologi'],
            ['nama' => 'Injeksi (Suntikan) oleh petugas', 'kategori' => 'Farmakologi'],

            // Edukasi
            ['nama' => 'Edukasi pola makan sehat', 'kategori' => 'Edukasi'],
            ['nama' => 'Edukasi kebersihan diri dan lingkungan', 'kategori' => 'Edukasi'],
            ['nama' => 'Edukasi istirahat yang cukup', 'kategori' => 'Edukasi'],
            ['nama' => 'Edukasi cara mencuci tangan yang benar', 'kategori' => 'Edukasi'],
            ['nama' => 'Konseling kesehatan mental', 'kategori' => 'Edukasi'],
            ['nama' => 'Edukasi manajemen stres', 'kategori' => 'Edukasi'],
            ['nama' => 'Edukasi pencegahan penularan penyakit', 'kategori' => 'Edukasi'],

            // Rujukan & Izin
            ['nama' => 'Izin rawat inap di UKS', 'kategori' => 'Rujukan & Izin'],
            ['nama' => 'Rujukan ke Puskesmas', 'kategori' => 'Rujukan & Izin'],
            ['nama' => 'Rujukan ke Rumah Sakit Umum', 'kategori' => 'Rujukan & Izin'],
            ['nama' => 'Rujukan ke dokter spesialis', 'kategori' => 'Rujukan & Izin'],
            ['nama' => 'Izin pulang ke rumah untuk pemulihan', 'kategori' => 'Rujukan & Izin'],
            ['nama' => 'Surat keterangan sakit', 'kategori' => 'Rujukan & Izin'],
            ['nama' => 'Notifikasi / Laporan ke wali santri', 'kategori' => 'Rujukan & Izin'],
        ];

        foreach ($tindakanList as $t) {
            TindakanMaster::firstOrCreate(['nama' => $t['nama']], $t);
        }

        $this->command->info('✅ Medical Master Data seeded: ' .
            Diagnosa::count() . ' Diagnosa, ' .
            KeluhanMaster::count() . ' Keluhan, ' .
            TindakanMaster::count() . ' Tindakan'
        );
    }
}
