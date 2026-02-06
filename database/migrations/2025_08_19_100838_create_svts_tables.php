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
            $table->string('profile_image')->nullable();
            $table->string('status', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        // =========================
        // Offense
        // =========================
        Schema::create('tbl_offense', function (Blueprint $table) {
            $table->bigIncrements('offense_id');
            $table->string('offense_type', 255);
            $table->text('offense_description');
            $table->timestamps();
            $table->softDeletes();
        });

        // =========================
        // Sanction
        // =========================
        Schema::create('tbl_sanction', function (Blueprint $table) {
            $table->bigIncrements('sanction_id');
            $table->text('sanction_consequences');
            $table->text('sanction_description');
            $table->timestamps();
            $table->softDeletes();
        });


        // =========================
        // Sanction Stages
        // =========================
        Schema::create('tbl_offense_with_sanction_stages', function (Blueprint $table) {
            $table->bigIncrements('owss_id');
            $table->unsignedBigInteger('offense_id');
            $table->unsignedBigInteger('sanction_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offense_id')->references('offense_id')->on('tbl_offense')->onDelete('cascade');
            $table->foreign('sanction_id')->references('sanction_id')->on('tbl_sanction')->onDelete('cascade');
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
            $table->string('profile_image')->nullable();
            $table->string('adviser_section', 255);
            $table->string('adviser_gradelevel', 50);
            $table->string('status', 100);
            $table->timestamps();
            $table->softDeletes();
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
            $table->string('status', 100);
            $table->timestamps();
            $table->softDeletes();
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
            $table->string('status', 100);
            $table->timestamps();
            $table->softDeletes();

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
            $table->unsignedBigInteger('offense_id');
            $table->unsignedBigInteger('sanction_id');
            $table->text('violation_incident');
            $table->date('violation_date');
            $table->time('violation_time');
            $table->string('status', 100);
            $table->enum('handled_by', ['adviser', 'prefect'])->default('adviser'); // ← NEW COLUMN
            $table->timestamp('escalated_at')->nullable(); // ← When escalated to prefect

            // Witness and Evidence columns
            $table->text('witnesses')->nullable(); // JSON or comma-separated witness names
            $table->text('complainant')->nullable(); // JSON or comma-separated complainant names ← NEW

            // Evidence columns
            $table->text('evidence_description')->nullable(); // Description of evidence
            $table->json('evidence_files')->nullable(); // JSON array of file paths

            // Sanction timing columns (using datetime)
            $table->datetime('sanction_start_at')->nullable(); // When sanction begins
            $table->datetime('sanction_end_at')->nullable(); // When sanction ends/fulfilled
// In your create_tbl_violation_record_table migration
$table->enum('sanction_status', ['pending', 'ongoing', 'neglected', 'completed', 'dismissed'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('violator_id')->references('student_id')->on('tbl_student')->onDelete('cascade');
            $table->foreign('prefect_id')->references('prefect_id')->on('tbl_prefect_of_discipline')->onDelete('cascade');
            $table->foreign('offense_id')->references('offense_id')->on('tbl_offense')->onDelete('cascade');
            $table->foreign('sanction_id')->references('sanction_id')->on('tbl_sanction')->onDelete('cascade');
        });

        // =========================
        // Violation Appointment
        // =========================
        Schema::create('tbl_violation_appointment', function (Blueprint $table) {
            $table->bigIncrements('violation_app_id');
            $table->unsignedBigInteger('violation_id');
            $table->enum('handled_by', ['adviser', 'prefect'])->default('adviser');
            $table->date('violation_app_date');
            $table->time('violation_app_time');
            $table->text('violation_app_notes')->nullable();
            $table->string('violation_app_status', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('violation_id')->references('violation_id')->on('tbl_violation_record')->onDelete('cascade');
        });

        // =========================
        // Violation Anecdotal
        // =========================
        Schema::create('tbl_violation_anecdotal', function (Blueprint $table) {
            $table->bigIncrements('violation_anec_id');
            $table->unsignedBigInteger('violation_id');
            $table->enum('handled_by', ['adviser', 'prefect'])->default('adviser'); // ← ADD THIS
            $table->text('violation_anec_solution');
            $table->text('violation_anec_recommendation');
            $table->date('violation_anec_date');
            $table->time('violation_anec_time');
            $table->string('status', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('violation_id')->references('violation_id')->on('tbl_violation_record')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in REVERSE order - children first, then parents
        Schema::dropIfExists('tbl_violation_anecdotal');
        Schema::dropIfExists('tbl_violation_appointment');
        Schema::dropIfExists('tbl_violation_record');
        Schema::dropIfExists('tbl_student');
        Schema::dropIfExists('tbl_parent');
        Schema::dropIfExists('tbl_adviser');
        Schema::dropIfExists('tbl_offense_with_sanction_stages');
        Schema::dropIfExists('tbl_sanction');
        Schema::dropIfExists('tbl_offense');
        Schema::dropIfExists('tbl_prefect_of_discipline');
    }
};
