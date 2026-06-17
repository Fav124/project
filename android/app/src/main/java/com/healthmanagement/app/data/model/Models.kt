package com.healthmanagement.app.data.model

import com.google.gson.annotations.SerializedName

data class ApiResponse<T>(
    val success: Boolean,
    val message: String?,
    val data: T?
)

data class PaginatedResponse<T>(
    val current_page: Int,
    val data: List<T>,
    val last_page: Int,
    val per_page: Int,
    val total: Int,
    val from: Int?,
    val to: Int?
)

data class LoginResponse(
    val token: String,
    val user: User
)

data class User(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    val status: String?,
    @SerializedName("no_hp") val noHp: String?,
    @SerializedName("profile_photo_url") val profilePhotoUrl: String?,
    @SerializedName("job_title") val jobTitle: String?,
    @SerializedName("created_at") val createdAt: String?
)

data class Santri(
    val id: Int,
    val nis: String,
    val name: String,
    val gender: String?,
    @SerializedName("birth_place") val birthPlace: String?,
    @SerializedName("birth_date") val birthDate: String?,
    @SerializedName("class_name") val className: String?,
    @SerializedName("major_name") val majorName: String?,
    @SerializedName("class_room") val classRoom: String?,
    @SerializedName("guardian_name") val guardianName: String?,
    @SerializedName("guardian_phone") val guardianPhone: String?,
    @SerializedName("blood_type") val bloodType: String?,
    @SerializedName("photo_url") val photoUrl: String?,
    val age: Int?,
    val gender_label: String?
)

data class SantriDetail(
    val id: Int,
    val nis: String,
    val name: String,
    val gender: String?,
    @SerializedName("birth_place") val birthPlace: String?,
    @SerializedName("birth_date") val birthDate: String?,
    @SerializedName("class_id") val classId: Int?,
    @SerializedName("major_id") val majorId: Int?,
    @SerializedName("class_name") val className: String?,
    @SerializedName("major_name") val majorName: String?,
    @SerializedName("class_room") val classRoom: String?,
    @SerializedName("guardian_name") val guardianName: String?,
    @SerializedName("guardian_phone") val guardianPhone: String?,
    @SerializedName("guardian_relationship") val guardianRelationship: String?,
    @SerializedName("guardian_job") val guardianJob: String?,
    @SerializedName("guardian_address") val guardianAddress: String?,
    @SerializedName("blood_type") val bloodType: String?,
    val height: String?,
    val weight: String?,
    val allergies: String?,
    @SerializedName("medical_history") val medicalHistory: String?,
    @SerializedName("special_condition") val specialCondition: String?,
    val notes: String?,
    @SerializedName("photo_url") val photoUrl: String?,
    val age: Int?,
    val gender_label: String?,
    @SerializedName("sickness_cases") val sicknessCases: List<SicknessCase>?,
    val guardians: List<Guardian>?
)

data class SantriLookups(
    val kelas: List<Kelas>,
    val jurusan: List<Jurusan>
)

data class Guardian(
    val id: Int,
    @SerializedName("santri_id") val santriId: Int,
    val name: String,
    val relationship: String?,
    val phone: String?,
    val address: String?,
    val job: String?,
    @SerializedName("is_primary") val isPrimary: Boolean?
)

data class Kelas(
    val id: Int,
    val name: String,
    val description: String?,
    val majors: List<Jurusan>?
)

data class Jurusan(
    val id: Int,
    val name: String,
    val description: String?,
    val classes: List<Kelas>?
)

data class DashboardSummary(
    @SerializedName("total_santri") val totalSantri: Int,
    @SerializedName("active_cases") val activeCases: Int,
    @SerializedName("today_visits") val todayVisits: Int,
    @SerializedName("recovered_today") val recoveredToday: Int,
    @SerializedName("low_stock_medicines") val lowStockMedicines: Int,
    @SerializedName("expiring_medicines") val expiringMedicines: Int,
    @SerializedName("pending_referrals") val pendingReferrals: Int,
    @SerializedName("total_referrals") val totalReferrals: Int,
    @SerializedName("recent_cases") val recentCases: List<SicknessCase>?,
    @SerializedName("status_distribution") val statusDistribution: Map<String, Int>?,
    @SerializedName("monthly_trend") val monthlyTrend: List<MonthlyTrend>?,
    @SerializedName("top_diagnoses") val topDiagnoses: List<DiagnosisCount>?
)

data class MonthlyTrend(
    val month: String,
    val year: Int?,
    val total: Int
)

data class DiagnosisCount(
    val diagnosis: String,
    val total: Int
)

data class SicknessCase(
    val id: Int,
    @SerializedName("santri_id") val santriId: Int?,
    @SerializedName("santri_name") val santriName: String?,
    @SerializedName("santri_nis") val santriNis: String?,
    @SerializedName("visit_date") val visitDate: String?,
    val diagnosis: String?,
    val complaint: String?,
    val status: String?,
    @SerializedName("status_label") val statusLabel: String?,
    @SerializedName("handled_by_name") val handledByName: String?,
    @SerializedName("created_at") val createdAt: String?
)

data class SicknessCaseDetail(
    val id: Int,
    @SerializedName("santri_id") val santriId: Int?,
    val santri: Santri?,
    @SerializedName("handled_by") val handledBy: Int?,
    val handler: User?,
    @SerializedName("visit_date") val visitDate: String?,
    @SerializedName("return_date") val returnDate: String?,
    val complaint: String?,
    val diagnosis: String?,
    @SerializedName("action_taken") val actionTaken: String?,
    @SerializedName("medicine_notes") val medicineNotes: String?,
    val status: String?,
    @SerializedName("status_label") val statusLabel: String?,
    val notes: String?,
    @SerializedName("photo_url") val photoUrl: String?,
    @SerializedName("hospital_name") val hospitalName: String?,
    val transport: String?,
    @SerializedName("companion_name") val companionName: String?,
    @SerializedName("picked_up_by") val pickedUpBy: String?,
    @SerializedName("picked_up_at") val pickedUpAt: String?,
    @SerializedName("discharge_notes") val dischargeNotes: String?,
    @SerializedName("discharge_guardian_id") val dischargeGuardianId: Int?,
    val medicines: List<SicknessMedicine>?,
    @SerializedName("keluhans") val keluhans: List<String>?,
    @SerializedName("diagnosas") val diagnosas: List<String>?,
    @SerializedName("tindakans") val tindakans: List<String>?
)

data class SicknessMedicine(
    val id: Int,
    @SerializedName("medicine_id") val medicineId: Int,
    @SerializedName("medicine_name") val medicineName: String,
    @SerializedName("medicine_kode") val medicineKode: String?,
    val dosage: String?,
    val quantity: Int,
    val frequency: String?,
    val duration: String?,
    val notes: String?,
    val status: String?
)

data class KunjunganFormData(
    val santris: List<Santri>,
    val medicines: List<Medicine>,
    val keluhans: List<LookupItem>,
    val diagnosas: List<LookupItem>,
    val tindakans: List<LookupItem>
)

data class LookupItem(
    val id: Int,
    val name: String
)

data class Medicine(
    val id: Int,
    @SerializedName("kode_obat") val kodeObat: String?,
    val name: String,
    val kategori: String?,
    @SerializedName("bentuk_sediaan") val bentukSediaan: String?,
    val unit: String?,
    val stock: Int,
    @SerializedName("minimum_stock") val minimumStock: Int,
    @SerializedName("expiry_date") val expiryDate: String?,
    @SerializedName("lokasi_penyimpanan") val lokasiPenyimpanan: String?
)

data class MedicineDetail(
    val id: Int,
    @SerializedName("kode_obat") val kodeObat: String?,
    val name: String,
    val kategori: String?,
    @SerializedName("bentuk_sediaan") val bentukSediaan: String?,
    val unit: String?,
    val stock: Int,
    @SerializedName("minimum_stock") val minimumStock: Int,
    @SerializedName("expiry_date") val expiryDate: String?,
    @SerializedName("lokasi_penyimpanan") val lokasiPenyimpanan: String?,
    val description: String?,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?,
    val mutations: List<MedicineMutation>?,
    val batches: List<MedicineBatch>?
)

data class MedicineMutation(
    val id: Int,
    @SerializedName("medicine_id") val medicineId: Int,
    val type: String?,
    val amount: Int,
    @SerializedName("before_stock") val beforeStock: Int,
    @SerializedName("after_stock") val afterStock: Int,
    val notes: String?,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("user_name") val userName: String?
)

data class MedicineBatch(
    val id: Int,
    @SerializedName("medicine_id") val medicineId: Int,
    @SerializedName("batch_number") val batchNumber: String?,
    val quantity: Int,
    @SerializedName("expiry_date") val expiryDate: String?,
    @SerializedName("received_date") val receivedDate: String?,
    val notes: String?
)

data class HospitalReferral(
    val id: Int,
    @SerializedName("santri_id") val santriId: Int,
    @SerializedName("santri_name") val santriName: String?,
    @SerializedName("hospital_name") val hospitalName: String,
    @SerializedName("referral_date") val referralDate: String?,
    val status: String?,
    @SerializedName("status_label") val statusLabel: String?,
    @SerializedName("referred_by_name") val referredByName: String?
)

data class HospitalReferralDetail(
    val id: Int,
    @SerializedName("santri_id") val santriId: Int,
    val santri: Santri?,
    @SerializedName("hospital_name") val hospitalName: String,
    @SerializedName("referral_date") val referralDate: String?,
    val reason: String?,
    val diagnosis: String?,
    val status: String?,
    val transport: String?,
    @SerializedName("companion_name") val companionName: String?,
    val notes: String?,
    val referrer: User?,
    @SerializedName("created_at") val createdAt: String?
)

data class ReportSummary(
    @SerializedName("total_cases") val totalCases: Int,
    @SerializedName("total_recovered") val totalRecovered: Int,
    @SerializedName("total_referred") val totalReferred: Int,
    @SerializedName("total_medicines_used") val totalMedicinesUsed: Int,
    @SerializedName("top_diagnoses") val topDiagnoses: List<DiagnosisCount>?,
    @SerializedName("daily_data") val dailyData: List<DailyReport>?,
    @SerializedName("monthly_data") val monthlyData: List<MonthlyReport>?
)

data class DailyReport(
    val date: String?,
    val total: Int
)

data class MonthlyReport(
    val month: String?,
    val total: Int
)

data class MedicineReport(
    @SerializedName("total_medicines") val totalMedicines: Int,
    @SerializedName("total_mutations") val totalMutations: Int,
    @SerializedName("total_in") val totalIn: Int,
    @SerializedName("total_out") val totalOut: Int,
    @SerializedName("low_stock_count") val lowStockCount: Int,
    @SerializedName("usage_data") val usageData: List<MedicineUsage>?
)

data class MedicineUsage(
    @SerializedName("medicine_name") val medicineName: String,
    @SerializedName("total_used") val totalUsed: Int
)

data class UserApproval(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    val status: String,
    @SerializedName("no_hp") val noHp: String?,
    @SerializedName("job_title") val jobTitle: String?,
    @SerializedName("created_at") val createdAt: String?
)
