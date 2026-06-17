package com.healthmanagement.app.data.api

import com.healthmanagement.app.data.model.*
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.Response
import retrofit2.http.*

interface ApiService {
    // Auth
    @POST("auth/login")
    suspend fun login(@Body body: Map<String, String>): Response<ApiResponse<LoginResponse>>

    @POST("auth/register")
    suspend fun register(@Body body: Map<String, String>): Response<ApiResponse<User>>

    @POST("auth/logout")
    suspend fun logout(): Response<ApiResponse<Any>>

    @GET("auth/me")
    suspend fun getMe(): Response<ApiResponse<User>>

    // Dashboard
    @GET("dashboard/summary")
    suspend fun getDashboardSummary(): Response<ApiResponse<DashboardSummary>>

    // Santri
    @GET("santri")
    suspend fun getSantri(
        @Query("page") page: Int = 1,
        @Query("search") search: String? = null
    ): Response<ApiResponse<PaginatedResponse<Santri>>>

    @GET("santri/{id}")
    suspend fun getSantriDetail(@Path("id") id: Int): Response<ApiResponse<SantriDetail>>

    @GET("santri/lookups")
    suspend fun getSantriLookups(): Response<ApiResponse<SantriLookups>>

    @Multipart
    @POST("santri")
    suspend fun createSantri(
        @Part("nis") nis: RequestBody,
        @Part("name") name: RequestBody,
        @Part("gender") gender: RequestBody,
        @Part("birth_place") birthPlace: RequestBody,
        @Part("birth_date") birthDate: RequestBody,
        @Part("class_id") classId: RequestBody,
        @Part("major_id") majorId: RequestBody,
        @Part("class_room") classRoom: RequestBody?,
        @Part("guardian_name") guardianName: RequestBody?,
        @Part("guardian_phone") guardianPhone: RequestBody?,
        @Part("guardian_relationship") guardianRelationship: RequestBody?,
        @Part("blood_type") bloodType: RequestBody?,
        @Part("height") height: RequestBody?,
        @Part("weight") weight: RequestBody?,
        @Part("allergies") allergies: RequestBody?,
        @Part("medical_history") medicalHistory: RequestBody?,
        @Part("notes") notes: RequestBody?,
        @Part photo: MultipartBody.Part?
    ): Response<ApiResponse<Santri>>

    @Multipart
    @POST("santri/{id}")
    @HTTP(method = "POST", path = "santri/{id}", hasBody = true)
    suspend fun updateSantri(
        @Path("id") id: Int,
        @Part("_method") method: RequestBody,
        @Part("nis") nis: RequestBody,
        @Part("name") name: RequestBody,
        @Part("gender") gender: RequestBody,
        @Part("birth_place") birthPlace: RequestBody,
        @Part("birth_date") birthDate: RequestBody,
        @Part("class_id") classId: RequestBody,
        @Part("major_id") majorId: RequestBody,
        @Part("class_room") classRoom: RequestBody?,
        @Part("guardian_name") guardianName: RequestBody?,
        @Part("guardian_phone") guardianPhone: RequestBody?,
        @Part("guardian_relationship") guardianRelationship: RequestBody?,
        @Part("blood_type") bloodType: RequestBody?,
        @Part("height") height: RequestBody?,
        @Part("weight") weight: RequestBody?,
        @Part("allergies") allergies: RequestBody?,
        @Part("medical_history") medicalHistory: RequestBody?,
        @Part("notes") notes: RequestBody?,
        @Part photo: MultipartBody.Part?
    ): Response<ApiResponse<Santri>>

    @DELETE("santri/{id}")
    suspend fun deleteSantri(@Path("id") id: Int): Response<ApiResponse<Any>>

    // Santri Guardians
    @GET("santri/{id}/guardians")
    suspend fun getGuardians(@Path("id") santriId: Int): Response<ApiResponse<List<Guardian>>>

    @POST("santri/{id}/guardians")
    suspend fun addGuardian(
        @Path("id") santriId: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Guardian>>

    @PUT("santri/{santriId}/guardians/{guardianId}")
    suspend fun updateGuardian(
        @Path("santriId") santriId: Int,
        @Path("guardianId") guardianId: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Guardian>>

    @DELETE("santri/{santriId}/guardians/{guardianId}")
    suspend fun deleteGuardian(
        @Path("santriId") santriId: Int,
        @Path("guardianId") guardianId: Int
    ): Response<ApiResponse<Any>>

    // Master Data - Kelas
    @GET("master/kelas")
    suspend fun getKelas(): Response<ApiResponse<List<Kelas>>>

    @POST("master/kelas")
    suspend fun createKelas(@Body body: Map<String, String>): Response<ApiResponse<Kelas>>

    @PUT("master/kelas/{id}")
    suspend fun updateKelas(
        @Path("id") id: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Kelas>>

    @DELETE("master/kelas/{id}")
    suspend fun deleteKelas(@Path("id") id: Int): Response<ApiResponse<Any>>

    // Master Data - Jurusan
    @GET("master/jurusan")
    suspend fun getJurusan(): Response<ApiResponse<List<Jurusan>>>

    @POST("master/jurusan")
    suspend fun createJurusan(@Body body: Map<String, String>): Response<ApiResponse<Jurusan>>

    @PUT("master/jurusan/{id}")
    suspend fun updateJurusan(
        @Path("id") id: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Jurusan>>

    @DELETE("master/jurusan/{id}")
    suspend fun deleteJurusan(@Path("id") id: Int): Response<ApiResponse<Any>>

    // Sickness Cases
    @GET("kunjungan-form-data")
    suspend fun getKunjunganFormData(): Response<ApiResponse<KunjunganFormData>>

    @GET("kunjungan")
    suspend fun getKunjungan(
        @Query("page") page: Int = 1,
        @Query("status") status: String? = null,
        @Query("search") search: String? = null
    ): Response<ApiResponse<PaginatedResponse<SicknessCase>>>

    @GET("kunjungan/{id}")
    suspend fun getKunjunganDetail(@Path("id") id: Int): Response<ApiResponse<SicknessCaseDetail>>

    @POST("kunjungan")
    suspend fun createKunjungan(@Body body: Map<String, @JvmSuppressWildcards Any>): Response<ApiResponse<SicknessCase>>

    @POST("kunjungan/{id}")
    @HTTP(method = "POST", path = "kunjungan/{id}", hasBody = true)
    suspend fun updateKunjungan(
        @Path("id") id: Int,
        @Body body: Map<String, @JvmSuppressWildcards Any>
    ): Response<ApiResponse<SicknessCase>>

    @DELETE("kunjungan/{id}")
    suspend fun deleteKunjungan(@Path("id") id: Int): Response<ApiResponse<Any>>

    @POST("kunjungan/{id}/mark-recovered")
    suspend fun markRecovered(@Path("id") id: Int): Response<ApiResponse<Any>>

    @POST("kunjungan/{id}/notify-guardian")
    suspend fun notifyGuardianKunjungan(@Path("id") id: Int): Response<ApiResponse<Any>>

    @POST("kunjungan/{id}/discharge")
    suspend fun dischargeKunjungan(
        @Path("id") id: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Any>>

    @POST("kunjungan/{id}/refer")
    suspend fun referToHospital(
        @Path("id") id: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Any>>

    @PUT("kunjungan/medicine/{pivotId}/status")
    suspend fun updateMedicineStatus(
        @Path("pivotId") pivotId: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Any>>

    // Obat (Medicines)
    @GET("obat")
    suspend fun getObat(
        @Query("page") page: Int = 1,
        @Query("search") search: String? = null,
        @Query("kategori") kategori: String? = null
    ): Response<ApiResponse<PaginatedResponse<Medicine>>>

    @GET("obat/{id}")
    suspend fun getObatDetail(@Path("id") id: Int): Response<ApiResponse<MedicineDetail>>

    @POST("obat")
    suspend fun createObat(@Body body: Map<String, String>): Response<ApiResponse<Medicine>>

    @PUT("obat/{id}")
    suspend fun updateObat(
        @Path("id") id: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Medicine>>

    @DELETE("obat/{id}")
    suspend fun deleteObat(@Path("id") id: Int): Response<ApiResponse<Any>>

    @POST("obat/mutasi")
    suspend fun createMutasi(@Body body: Map<String, @JvmSuppressWildcards Any>): Response<ApiResponse<Any>>

    // Hospital Referrals
    @GET("rujukan")
    suspend fun getRujukan(
        @Query("page") page: Int = 1,
        @Query("status") status: String? = null
    ): Response<ApiResponse<PaginatedResponse<HospitalReferral>>>

    @GET("rujukan/{id}")
    suspend fun getRujukanDetail(@Path("id") id: Int): Response<ApiResponse<HospitalReferralDetail>>

    @POST("rujukan")
    suspend fun createRujukan(@Body body: Map<String, @JvmSuppressWildcards Any>): Response<ApiResponse<HospitalReferral>>

    @PUT("rujukan/{id}")
    suspend fun updateRujukan(
        @Path("id") id: Int,
        @Body body: Map<String, @JvmSuppressWildcards Any>
    ): Response<ApiResponse<HospitalReferral>>

    @DELETE("rujukan/{id}")
    suspend fun deleteRujukan(@Path("id") id: Int): Response<ApiResponse<Any>>

    @PATCH("rujukan/{id}/status")
    suspend fun updateRujukanStatus(
        @Path("id") id: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Any>>

    @POST("rujukan/{id}/notify-guardian")
    suspend fun notifyGuardianRujukan(@Path("id") id: Int): Response<ApiResponse<Any>>

    // Reports
    @GET("reports/daily-summary")
    suspend fun getDailySummary(
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null,
        @Query("period") period: String? = null
    ): Response<ApiResponse<ReportSummary>>

    @GET("reports/medicine")
    suspend fun getMedicineReport(
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null
    ): Response<ApiResponse<MedicineReport>>

    // Approvals
    @GET("approvals")
    suspend fun getApprovals(): Response<ApiResponse<List<UserApproval>>>

    @POST("approvals/{user}/approve")
    suspend fun approveUser(@Path("user") userId: Int): Response<ApiResponse<Any>>

    @POST("approvals/{user}/reject")
    suspend fun rejectUser(
        @Path("user") userId: Int,
        @Body body: Map<String, String>
    ): Response<ApiResponse<Any>>
}
