<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Level;
use App\Models\Page;
use App\Models\PaymentType;
use App\Models\PracticalSession;
use App\Models\Question;
use App\Models\StudentApplication;
use App\Models\StudentPayment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo tenant: École de Conduite Bamenda...');

        // ===== 1. TENANT =====
        $tenantId = (string) Str::uuid();
        $now = now();

        DB::connection(config('tenancy.database.central_connection', 'pgsql'))
            ->table('tenants')->insert([
                'id'                           => $tenantId,
                'name'                         => 'École de Conduite Bamenda',
                'status'                       => 'active',
                'subdomain'                    => 'bamenda-driving',
                'desired_subdomain'            => 'bamenda-driving',
                'contact_name'                 => 'Jean Dupont',
                'contact_email'                => 'owner@bamenda-driving.cm',
                'contact_phone'                => '+237 670 100 100',
                'applicant_town'               => 'Bamenda',
                'submitted_at'                 => $now->copy()->subDays(45),
                'reviewed_at'                  => $now->copy()->subDays(43),
                'terms_agreed_at'              => $now->copy()->subDays(45),
                'momo_number'                  => '+237 670 100 100',
                'orange_number'                => '+237 690 100 100',
                'payment_instructions'         => "Send payment to one of the numbers above. Use the student's full name as the transfer reference. Upload the screenshot below after payment.",
                'billing_status'               => 'active',
                'current_billing_period_start' => $now->copy()->subDays(43),
                'next_billing_due'             => $now->copy()->subDays(43)->addDays(30),
                'data'                         => json_encode([]),
                'created_at'                   => $now->copy()->subDays(45),
                'updated_at'                   => $now,
            ]);

        Domain::create([
            'domain'    => 'bamenda-driving',
            'tenant_id' => $tenantId,
        ]);

        // Use session-bound tenant_id for the BelongsToTenant trait (other models).
        $previousTenantId = session('tenant_id');
        session(['tenant_id' => $tenantId]);

        try {
            // ===== 2. USERS =====
            $this->command->info('  Users...');
            $passwordHash = Hash::make('demo1234');  // shared dev password for the demo

            $owner = User::create([
                'tenant_id' => $tenantId,
                'name'      => 'Jean Dupont',
                'email'     => 'owner@bamenda-driving.cm',
                'phone'     => '+237 670 100 100',
                'town'      => 'Bamenda',
                'password'  => $passwordHash,
                'role'      => 'owner',
                'language'  => 'en',
                'must_change_password' => false,
            ]);

            $secretary1 = User::create([
                'tenant_id' => $tenantId, 'name' => 'Marie Tchamba', 'email' => 'marie@bamenda-driving.cm',
                'phone' => '+237 670 200 201', 'town' => 'Bamenda',
                'password' => $passwordHash, 'role' => 'secretary', 'language' => 'en', 'must_change_password' => false,
            ]);
            $secretary2 = User::create([
                'tenant_id' => $tenantId, 'name' => 'Pauline Nkemngu', 'email' => 'pauline@bamenda-driving.cm',
                'phone' => '+237 670 200 202', 'town' => 'Bamenda',
                'password' => $passwordHash, 'role' => 'secretary', 'language' => 'en', 'must_change_password' => false,
            ]);

            $instructors = collect([
                ['Paul Mbeki',         'paul@bamenda-driving.cm',       '+237 670 300 301'],
                ['Sandrine Ekosso',    'sandrine@bamenda-driving.cm',   '+237 670 300 302'],
                ['Kenneth Fonkou',     'kenneth@bamenda-driving.cm',    '+237 670 300 303'],
                ['Christine Mbah',     'christine@bamenda-driving.cm',  '+237 670 300 304'],
            ])->map(fn ($i) => User::create([
                'tenant_id' => $tenantId, 'name' => $i[0], 'email' => $i[1], 'phone' => $i[2], 'town' => 'Bamenda',
                'password' => $passwordHash, 'role' => 'instructor', 'language' => 'en', 'must_change_password' => false,
            ]));

            $studentData = [
                ['Aminatou Bello',     'aminatou@example.com',  '+237 690 400 401', 'Bamenda'],
                ['Emmanuel Tchana',    'emmanuel@example.com',  '+237 690 400 402', 'Bafoussam'],
                ['Florence Awumbom',   'florence@example.com',  '+237 690 400 403', 'Bamenda'],
                ['Brice Kamga',        'brice@example.com',     '+237 690 400 404', 'Douala'],
                ['Solange Mefire',     'solange@example.com',   '+237 690 400 405', 'Yaoundé'],
                ['Patrice Ndi',        'patrice@example.com',   '+237 690 400 406', 'Bamenda'],
                ['Charlotte Nyong',    'charlotte@example.com', '+237 690 400 407', 'Bamenda'],
                ['Ibrahim Nguini',     'ibrahim@example.com',   '+237 690 400 408', 'Garoua'],
                ['Mireille Tsafack',   'mireille@example.com',  '+237 690 400 409', 'Bamenda'],
                ['Stéphane Ouandji',   'stephane@example.com',  '+237 690 400 410', 'Bamenda'],
            ];
            $students = collect($studentData)->map(fn ($s) => User::create([
                'tenant_id' => $tenantId, 'name' => $s[0], 'email' => $s[1], 'phone' => $s[2], 'town' => $s[3],
                'password' => $passwordHash, 'role' => 'student', 'language' => 'en', 'must_change_password' => false,
            ]));

            // ===== 3. LEVELS + LESSONS + QUESTIONS =====
            $this->command->info('  Levels, lessons, questions...');

            $levelsData = [
                [
                    'name' => 'Level 1 — Basics',
                    'description' => 'Vehicle controls, basic road rules, vocabulary.',
                    'lessons' => [
                        ['Introduction to your vehicle', "Understanding your vehicle is the first step. Learn about the steering wheel, gear shift, brake pedal, and accelerator. Always perform a safety check before driving: tire pressure, fluids, lights, and mirrors.\n\nProper seating position matters: your knee should have a slight bend when the brake is fully depressed. Hands at 9 and 3 o'clock on the wheel."],
                        ['Starting and stopping', "Smooth starts and stops are the mark of a good driver. Always check your surroundings before moving. Engage the clutch (manual) or apply the brake (automatic), then start the engine.\n\nWhen stopping: ease off the accelerator early, brake smoothly, and downshift if manual. Avoid sudden stops except in emergencies."],
                        ['Steering and lane discipline', "Keep your eyes on the road ahead — not on the hood. Look where you want to go. Maintain your lane with smooth, small steering corrections.\n\nWhen changing lanes: signal first, check your mirrors, look over your shoulder for blind spots, then move."],
                        ['Mirrors and blind spots', "Mirrors give you situational awareness. Adjust your mirrors before driving: the side mirrors should show a sliver of your car. The rearview mirror should frame the rear window.\n\nBlind spots are areas mirrors don't cover. Always shoulder-check before changing lanes."],
                        ['Basic road vocabulary', "Know the terms: intersection, roundabout, pedestrian crossing, shoulder, median. Understand right-of-way: who goes first at intersections, who yields to whom.\n\nIn Cameroon, vehicles drive on the right side of the road."],
                    ],
                ],
                [
                    'name' => 'Level 2 — Road signs',
                    'description' => 'Regulatory, warning, and informational signs.',
                    'lessons' => [
                        ['Regulatory signs', "Regulatory signs tell you what you MUST do. Stop signs (octagon, red): full stop required. Yield signs (triangle, white/red): give way to other traffic. Speed limit signs (circle, white with number): the maximum allowed speed.\n\nNo entry, no parking, no U-turn — circular signs with red diagonal lines indicate prohibitions."],
                        ['Warning signs', "Warning signs alert you to upcoming conditions. They are usually diamond-shaped (or triangular with red border in Cameroon). Examples: curve ahead, intersection ahead, school zone, pedestrian crossing.\n\nReduce speed and stay alert when you see a warning sign."],
                        ['Informational and direction signs', "Blue or green rectangular signs give information: directions, distances to cities, hospital locations, fuel stations. They help you navigate.\n\nLearn to read them quickly so you don't miss turns."],
                        ['Road markings', "Painted markings on the road convey rules: solid white lines separate lanes going the same direction; solid yellow lines separate opposing traffic; dashed lines mean passing is allowed (when safe).\n\nA solid line you cannot cross. A dashed line you may cross when safe."],
                    ],
                ],
                [
                    'name' => 'Level 3 — Highway driving',
                    'description' => 'Highway entry, exit, lane discipline, overtaking.',
                    'lessons' => [
                        ['Entering the highway', "Use the on-ramp to accelerate to highway speed BEFORE merging. Signal early. Look for a gap in traffic — never stop on the on-ramp unless absolutely required.\n\nMerge smoothly into the right lane. Do not force other vehicles to brake for you."],
                        ['Lane discipline on highways', "On a multi-lane highway, slower traffic stays right; faster traffic uses left lanes for overtaking only. Do not 'cruise' in the left lane.\n\nMaintain a safe following distance: at least 3 seconds from the car ahead. More in rain or poor visibility."],
                        ['Overtaking safely', "Before overtaking: signal, check mirrors, shoulder-check, ensure the lane is clear. Accelerate past the vehicle, give 2+ seconds clearance, then signal and return to your lane.\n\nNever overtake on a curve, hill, or where visibility is limited."],
                        ['Exiting the highway', "Plan exits well in advance. Move to the right lane early. Signal before the exit ramp. Decelerate ONLY after you're on the exit ramp — not on the highway itself.\n\nObey the posted exit speed limit."],
                    ],
                ],
                [
                    'name' => 'Level 4 — Final preparation',
                    'description' => 'Defensive driving, emergency situations, final review.',
                    'lessons' => [
                        ['Defensive driving principles', "Anticipate other drivers' mistakes. Maintain space cushions on all sides of your vehicle. Scan the road 12-15 seconds ahead.\n\nNever assume another driver sees you. Make eye contact at intersections when possible."],
                        ['Driving in poor weather', "Rain: reduce speed, increase following distance, turn on headlights. Avoid sudden braking on wet roads.\n\nFog: use low beams (high beams reflect back). Reduce speed dramatically. Use the right-edge line as a guide if visibility is very low."],
                        ['Emergency situations', "Brake failure: pump the pedal, use engine braking (downshift), use parking brake gradually. Tire blowout: grip the wheel firmly, do NOT slam the brakes, ease off the accelerator, steer to the shoulder.\n\nKnow what to do BEFORE you need it."],
                        ['Final road test prep', "On test day: arrive early, bring required documents, be calm. The examiner is looking for safe, consistent driving — not perfection.\n\nCommon mistakes: not shoulder-checking, rolling stops, speeding even slightly, hesitation at intersections. Practice these areas."],
                        ['Cameroonian road code review', "Review the Code de la Route. Know speed limits: 50 km/h in town, 90 km/h on rural roads, 110 km/h on highways. Seatbelts mandatory for all occupants.\n\nDrinking and driving: zero tolerance for new drivers. Mobile phone use while driving is prohibited."],
                    ],
                ],
            ];

            $levelIndex = 0;
            foreach ($levelsData as $levelData) {
                $level = Level::create([
                    'tenant_id'   => $tenantId,
                    'name'        => $levelData['name'],
                    'description' => $levelData['description'],
                    'position'    => $levelIndex,
                ]);

                $lessonIndex = 0;
                foreach ($levelData['lessons'] as [$title, $content]) {
                    // Lesson content is stored as JSON block-editor structure. Use simple text block.
                    $contentJson = [
                        ['type' => 'paragraph', 'text' => $content],
                    ];

                    $lesson = Lesson::create([
                        'tenant_id'        => $tenantId,
                        'level_id'         => $level->id,
                        'title'            => $title,
                        'content'          => $contentJson,
                        'status'           => 'published',
                        'position'         => $lessonIndex,
                        'duration_minutes' => 15,
                    ]);

                    // 3-4 questions per lesson (text-only per scope §1.1 #2 a).
                    $questionCount = 3 + ($lessonIndex % 2); // 3 or 4
                    for ($q = 0; $q < $questionCount; $q++) {
                        $isTrueFalse = ($q === $questionCount - 1); // last one is T/F
                        $question = Question::create([
                            'tenant_id' => $tenantId,
                            'lesson_id' => $lesson->id,
                            'type'      => $isTrueFalse ? 'true_false' : 'mcq',
                            'prompt'    => $isTrueFalse
                                ? 'You should always check your mirrors before changing lanes.'
                                : "What is the correct action when approaching a yield sign?",
                            'position'  => $q,
                        ]);

                        if ($isTrueFalse) {
                            DB::table('question_options')->insert([
                                ['tenant_id' => $tenantId, 'question_id' => $question->id, 'text' => 'True',  'is_correct' => true,  'position' => 0],
                                ['tenant_id' => $tenantId, 'question_id' => $question->id, 'text' => 'False', 'is_correct' => false, 'position' => 1],
                            ]);
                        } else {
                            DB::table('question_options')->insert([
                                ['tenant_id' => $tenantId, 'question_id' => $question->id, 'text' => 'Speed up to pass quickly',           'is_correct' => false, 'position' => 0],
                                ['tenant_id' => $tenantId, 'question_id' => $question->id, 'text' => 'Slow down and give way to traffic',  'is_correct' => true,  'position' => 1],
                                ['tenant_id' => $tenantId, 'question_id' => $question->id, 'text' => 'Stop completely and wait 5 seconds', 'is_correct' => false, 'position' => 2],
                                ['tenant_id' => $tenantId, 'question_id' => $question->id, 'text' => 'Ignore the sign if road is empty',   'is_correct' => false, 'position' => 3],
                            ]);
                        }
                    }

                    $lessonIndex++;
                }

                $levelIndex++;
            }

            // ===== 4. STUDENT LESSON PROGRESS (varying states) =====
            $this->command->info('  Student progress...');

            $allPublishedLessons = Lesson::where('status', 'published')->orderBy('level_id')->orderBy('position')->get();

            // Student 0-2: completed all of level 1, partway through level 2
            // Student 3-4: completed all of levels 1+2, partway through level 3
            // Student 5-6: completed all of levels 1+2+3
            // Student 7-9: just started, only first lesson of level 1 done
            $progressTargets = [
                // [student_index, lesson_count_completed]
                [0, 7], [1, 7], [2, 6],
                [3, 13], [4, 12],
                [5, 17], [6, 17],
                [7, 1], [8, 1], [9, 2],
            ];

            foreach ($progressTargets as [$si, $count]) {
                $student = $students[$si];
                foreach ($allPublishedLessons->take($count) as $lesson) {
                    DB::table('lesson_progress')->insert([
                        'tenant_id'     => $tenantId,
                        'user_id'       => $student->id,
                        'lesson_id'     => $lesson->id,
                        'completed'     => true,
                        'attempt_count' => 1 + random_int(0, 2),
                        'best_score'    => random_int(75, 100),
                        'completed_at'  => $now->copy()->subDays(random_int(1, 30)),
                        'created_at'    => $now->copy()->subDays(random_int(20, 40)),
                        'updated_at'    => $now->copy()->subDays(random_int(1, 30)),
                    ]);
                }
            }

            // ===== 5. PRACTICAL SESSIONS =====
            $this->command->info('  Practical sessions...');

            // 25 sessions across last 14 days + next 7 days
            for ($i = 0; $i < 25; $i++) {
                $student = $students->random();
                $instructor = $instructors->random();
                $offset = random_int(-14, 7);
                $hour = [8, 10, 14, 16][random_int(0, 3)];
                $scheduledAt = $now->copy()->addDays($offset)->setTime($hour, 0);

                // Status: past sessions = mostly completed, few no-show; future sessions = scheduled.
                if ($offset < 0) {
                    $status = random_int(1, 10) <= 8 ? 'completed' : 'no_show';
                } else {
                    $status = 'scheduled';
                }

                PracticalSession::create([
                    'tenant_id'        => $tenantId,
                    'student_id'       => $student->id,
                    'instructor_id'    => $instructor->id,
                    'scheduled_at'     => $scheduledAt,
                    'duration_minutes' => 60,
                    'status'           => $status,
                    'completed_at'     => $status === 'completed' ? $scheduledAt->copy()->addHour() : null,
                ]);
            }

            // ===== 6. STUDENT APPLICATIONS =====
            $this->command->info('  Student applications...');

            $appNames = [
                ['pending',  'Daniel Atangana',   'daniel.a@example.com',   '+237 690 500 501', 'Bamenda'],
                ['pending',  'Esther Mbarga',     'esther.m@example.com',   '+237 690 500 502', 'Bafoussam'],
                ['pending',  'Robert Nkomo',      'robert.n@example.com',   '+237 690 500 503', 'Bamenda'],
                ['approved', 'Alice Yemelong',    'alice.y@example.com',    '+237 690 500 504', 'Bamenda'],
                ['approved', 'Bertin Foumbouet',  'bertin.f@example.com',   '+237 690 500 505', 'Yaoundé'],
                ['rejected', 'Claudine Mfoula',   'claudine.m@example.com', '+237 690 500 506', 'Douala'],
            ];

            foreach ($appNames as [$status, $name, $email, $phone, $town]) {
                $submittedAt = $now->copy()->subDays(random_int(1, 21));
                $reviewedAt = $status === 'pending' ? null : $submittedAt->copy()->addDays(random_int(1, 3));

                StudentApplication::create([
                    'tenant_id'        => $tenantId,
                    'name'             => $name,
                    'email'            => $email,
                    'phone'            => $phone,
                    'town'             => $town,
                    'desired_level_id' => null,
                    'source'           => 'public_form',
                    'status'           => $status,
                    'rejection_reason' => $status === 'rejected' ? 'Incomplete application — missing identification documents.' : null,
                    'submitted_at'     => $submittedAt,
                    'reviewed_at'      => $reviewedAt,
                    'reviewed_by'      => $reviewedAt ? $owner->id : null,
                ]);
            }

            // ===== 7. CMS PAGES =====
            $this->command->info('  Public site pages...');

            Page::create([
                'tenant_id' => $tenantId, 'slug' => 'home', 'title' => 'Welcome',
                'status'    => 'published', 'is_home' => true, 'position' => 0,
                'content'   => [['type' => 'paragraph', 'text' => "École de Conduite Bamenda — your driving school in the heart of the Northwest. We have been training safe drivers in Bamenda since 2018. Modern theory rooms, experienced instructors, well-maintained training vehicles."]],
            ]);
            Page::create([
                'tenant_id' => $tenantId, 'slug' => 'about', 'title' => 'About us',
                'status'    => 'published', 'is_home' => false, 'position' => 1,
                'content'   => [['type' => 'paragraph', 'text' => "Founded in 2018 by Jean Dupont, our school combines hands-on instruction with rigorous theory. We follow the Cameroonian Code de la Route and provide validated license-hours reports for all our students."]],
            ]);
            Page::create([
                'tenant_id' => $tenantId, 'slug' => 'programs', 'title' => 'Programs',
                'status'    => 'published', 'is_home' => false, 'position' => 2,
                'content'   => [['type' => 'paragraph', 'text' => "We offer Category B (passenger vehicles) and Category A (motorcycles) training. Our programs combine 4 levels of theory with structured practical sessions. Average completion: 6-10 weeks."]],
            ]);

            // ===== 8. PAYMENT TYPES =====
            $this->command->info('  Payment types...');

            $enrollmentFee = PaymentType::create([
                'tenant_id'                     => $tenantId,
                'name'                          => "Frais d'inscription",
                'description'                   => 'Enrollment fee for all students. Prompted after completing level 2.',
                'amount_xaf'                    => 50000,
                'is_required'                   => true,
                'levels_required_before_prompt' => 2,
                'is_active'                     => true,
                'sort_order'                    => 0,
            ]);

            PaymentType::create([
                'tenant_id'                     => $tenantId,
                'name'                          => 'Permis de conduire (processing)',
                'description'                   => 'Optional license processing fee. Pay only if you want our office to handle filing for you.',
                'amount_xaf'                    => 25000,
                'is_required'                   => false,
                'levels_required_before_prompt' => null,
                'is_active'                     => true,
                'sort_order'                    => 1,
            ]);

            // ===== 9. STUDENT PAYMENTS (varying states) =====
            $this->command->info('  Student payments...');

            // Student 5+6 finished level 3, so they'd have paid the enrollment fee. Mark approved.
            foreach ([5, 6] as $si) {
                StudentPayment::create([
                    'tenant_id'       => $tenantId,
                    'student_id'      => $students[$si]->id,
                    'payment_type_id' => $enrollmentFee->id,
                    'status'          => 'approved',
                    'amount_xaf'      => 50000,
                    'created_via'     => 'student_upload',
                    'submitted_at'    => $now->copy()->subDays(8),
                    'reviewed_at'     => $now->copy()->subDays(7),
                    'reviewed_by'     => $owner->id,
                ]);
            }
            // Student 3+4 are in level 3 but haven't paid yet — they're CURRENTLY BLOCKED.
            // (No payment record = pending required payment exists, gate triggers.)

            // Student 2 has a pending submission awaiting review.
            StudentPayment::create([
                'tenant_id'       => $tenantId,
                'student_id'      => $students[2]->id,
                'payment_type_id' => $enrollmentFee->id,
                'status'          => 'pending_review',
                'amount_xaf'      => 50000,
                'created_via'     => 'student_upload',
                'submitted_at'    => $now->copy()->subDays(1),
                'notes'           => 'Paid via MoMo this morning.',
            ]);

            // Student 1 was rejected and needs to re-submit.
            StudentPayment::create([
                'tenant_id'        => $tenantId,
                'student_id'       => $students[1]->id,
                'payment_type_id'  => $enrollmentFee->id,
                'status'           => 'rejected',
                'amount_xaf'       => 50000,
                'created_via'      => 'student_upload',
                'submitted_at'     => $now->copy()->subDays(3),
                'reviewed_at'      => $now->copy()->subDays(2),
                'reviewed_by'      => $owner->id,
                'rejection_reason' => 'Screenshot was too blurry to read the amount. Please re-submit with a clearer image.',
            ]);

            // Student 0 paid the optional license fee (manual mark — cash).
            StudentPayment::create([
                'tenant_id'       => $tenantId,
                'student_id'      => $students[0]->id,
                'payment_type_id' => PaymentType::where('is_required', false)->first()->id,
                'status'          => 'approved',
                'amount_xaf'      => 25000,
                'notes'           => 'Paid in cash at the office on June 5.',
                'created_via'     => 'manual_mark',
                'submitted_at'    => $now->copy()->subDays(5),
                'reviewed_at'     => $now->copy()->subDays(5),
                'reviewed_by'     => $owner->id,
            ]);

        } finally {
            // Restore previous tenant session.
            session(['tenant_id' => $previousTenantId]);
        }

        $this->command->info('');
        $this->command->info('Demo tenant seeded successfully:');
        $this->command->info('  Subdomain:  bamenda-driving.lvh.me (dev) | bamenda-driving.drivecm.cm (prod)');
        $this->command->info('  Owner:      owner@bamenda-driving.cm');
        $this->command->info('  Password:   demo1234 (all users)');
        $this->command->info('');
    }
}
