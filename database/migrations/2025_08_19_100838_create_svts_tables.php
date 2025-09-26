<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =========================
        // Prefect of Discipline
        // =========================
        Schema::create('tbl_prefect_of_discipline', function (Blueprint $table) {
            $table->bigIncrements('prefect_id');
            $table->string('prefect_fname', 255);
            $table->string('prefect_lname', 255);
            $table->enum('prefect_sex', ['male', 'female', 'other'])->nullable();
            $table->string('prefect_email', 255);
            $table->string('prefect_password', 255);
            $table->string('prefect_contactinfo', 255);
            $table->string('status', 50)->default('active');
            $table->timestamps();
        });

        // =========================
        // Offenses with Sanction
        // =========================
        Schema::create('tbl_offenses_with_sanction', function (Blueprint $table) {
    $table->bigIncrements('offense_sanc_id');
    $table->string('offense_type', 255);
    $table->text('offense_description');
    $table->text('sanction_consequences');
    $table->integer('group_number')->default(1);  // 👈 Add this
    $table->integer('stage_number')->default(1);  // 👈 Add this
    $table->timestamps();
});
        // =========================
        // Adviser
        // =========================
        Schema::create('tbl_adviser', function (Blueprint $table) {
            $table->bigIncrements('adviser_id');
            $table->string('adviser_fname', 255);
            $table->string('adviser_lname', 255);
            $table->enum('adviser_sex', ['male', 'female', 'other'])->nullable();
            $table->string('adviser_email', 255);
            $table->string('adviser_password', 255);
            $table->string('adviser_contactinfo', 255);
            $table->string('adviser_section', 255);
            $table->string('adviser_gradelevel', 50);
            $table->string('status', 50)->default('active');
            $table->timestamps();
        });

        // =========================
        // Parent
        // =========================
        Schema::create('tbl_parent', function (Blueprint $table) {
            $table->bigIncrements('parent_id');
            $table->string('parent_fname', 255);
            $table->string('parent_lname', 255);
            $table->enum('parent_sex', ['male', 'female', 'other'])->nullable();
            $table->date('parent_birthdate');
            $table->string('parent_email', 255)->nullable();
            $table->string('parent_contactinfo', 255);
            $table->string('parent_relationship', 50)->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamps();
        });

        // =========================
        // Student
        // =========================
        Schema::create('tbl_student', function (Blueprint $table) {
            $table->bigIncrements('student_id');
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('adviser_id');
            $table->string('student_fname', 255);
            $table->string('student_lname', 255);
            $table->enum('student_sex', ['male', 'female', 'other'])->nullable();
            $table->date('student_birthdate');
            $table->string('student_address', 255);
            $table->string('student_contactinfo', 255);
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('parent_id')->references('parent_id')->on('tbl_parent')->onDelete('cascade');
            $table->foreign('adviser_id')->references('adviser_id')->on('tbl_adviser')->onDelete('cascade');
        });

        // =========================
        // Violation Record
        // =========================
        Schema::create('tbl_violation_record', function (Blueprint $table) {
            $table->bigIncrements('violation_id');
            $table->unsignedBigInteger('violator_id');
            $table->unsignedBigInteger('prefect_id');
            $table->unsignedBigInteger('offense_sanc_id');
            $table->text('violation_incident');
            $table->date('violation_date');
            $table->time('violation_time');
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('violator_id')->references('student_id')->on('tbl_student')->onDelete('cascade');
            $table->foreign('prefect_id')->references('prefect_id')->on('tbl_prefect_of_discipline')->onDelete('cascade');
            $table->foreign('offense_sanc_id')->references('offense_sanc_id')->on('tbl_offenses_with_sanction')->onDelete('cascade');
        });

        // =========================
        // Violation Appointment
        // =========================
        Schema::create('tbl_violation_appointment', function (Blueprint $table) {
            $table->bigIncrements('violation_app_id');
            $table->unsignedBigInteger('violation_id');
            $table->date('violation_app_date');
            $table->time('violation_app_time');
            $table->string('violation_app_status', 100);
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('violation_id')->references('violation_id')->on('tbl_violation_record')->onDelete('cascade');
        });

        // =========================
        // Violation Anecdotal
        // =========================
        Schema::create('tbl_violation_anecdotal', function (Blueprint $table) {
            $table->bigIncrements('violation_anec_id');
            $table->unsignedBigInteger('violation_id');
            $table->text('violation_anec_solution');
            $table->text('violation_anec_recommendation');
            $table->date('violation_anec_date');
            $table->time('violation_anec_time');
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('violation_id')->references('violation_id')->on('tbl_violation_record')->onDelete('cascade');
        });

        // =========================
        // Complaints
        // =========================
        Schema::create('tbl_complaints', function (Blueprint $table) {
            $table->bigIncrements('complaints_id');
            $table->unsignedBigInteger('complainant_id');
            $table->unsignedBigInteger('respondent_id');
            $table->unsignedBigInteger('prefect_id');
            $table->unsignedBigInteger('offense_sanc_id');
            $table->text('complaints_incident');
            $table->date('complaints_date');
            $table->time('complaints_time');
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('complainant_id')->references('student_id')->on('tbl_student')->onDelete('cascade');
            $table->foreign('respondent_id')->references('student_id')->on('tbl_student')->onDelete('cascade');
            $table->foreign('prefect_id')->references('prefect_id')->on('tbl_prefect_of_discipline')->onDelete('cascade');
            $table->foreign('offense_sanc_id')->references('offense_sanc_id')->on('tbl_offenses_with_sanction')->onDelete('cascade');
        });

        // =========================
        // Complaints Appointment
        // =========================
        Schema::create('tbl_complaints_appointment', function (Blueprint $table) {
            $table->bigIncrements('comp_app_id');
            $table->unsignedBigInteger('complaints_id');
            $table->date('comp_app_date');
            $table->time('comp_app_time');
            $table->string('comp_app_status', 100);
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('complaints_id')->references('complaints_id')->on('tbl_complaints')->onDelete('cascade');
        });

        // =========================
        // Complaints Anecdotal
        // =========================
        Schema::create('tbl_complaints_anecdotal', function (Blueprint $table) {
            $table->bigIncrements('comp_anec_id');
            $table->unsignedBigInteger('complaints_id');
            $table->text('comp_anec_solution');
            $table->text('comp_anec_recommendation');
            $table->date('comp_anec_date');
            $table->time('comp_anec_time');
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('complaints_id')->references('complaints_id')->on('tbl_complaints')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_complaints_anecdotal');
        Schema::dropIfExists('tbl_complaints_appointment');
        Schema::dropIfExists('tbl_complaints');
        Schema::dropIfExists('tbl_violation_anecdotal');
        Schema::dropIfExists('tbl_violation_appointment');
        Schema::dropIfExists('tbl_violation_record');
        Schema::dropIfExists('tbl_student');
        Schema::dropIfExists('tbl_parent');
        Schema::dropIfExists('tbl_adviser');
        Schema::dropIfExists('tbl_offenses_with_sanction');
        Schema::dropIfExists('tbl_prefect_of_discipline');
    }
};
