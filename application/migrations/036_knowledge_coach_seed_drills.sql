-- ============================================================================
-- STEM CRM - Migration 036 - Seed: coaching_drill (28 rows)
-- 2 drills per skill x 14 skills = 28 rows
-- One beginner drill + one advanced drill per skill
-- ============================================================================
-- Plain English. No em-dashes. No non-ASCII characters.
-- Idempotent: INSERT IGNORE on drill_code UNIQUE KEY.
-- llm_rubric_path: path to the LLM evaluation rubric JSON under stem_coach_rubrics/drills/
-- estimated_minutes: approximate time for BD to complete the drill
-- Author: STEM ops
-- Date: 2026-05-18
-- ============================================================================

INSERT IGNORE INTO coaching_drill
  (drill_code, skill_code, drill_type, level, title,
   prompt_for_bd, success_criteria, content_url, llm_rubric_path,
   estimated_minutes, active)
VALUES

-- ============================================================================
-- SKILL: PROSPECTING
-- ============================================================================
('PROS_BEG_01',
 'PROSPECTING',
 'script',
 'beginner',
 'Build a 10-school prospect list from your territory map',
 'Using the district school directory (link in STEM Learner Portal under Resources), identify 10 schools in your assigned territory that: (1) have more than 300 students, (2) do not already appear in your CRM as active leads, and (3) have at least one indicator of STEM interest (science club, robotics team, or government scheme mention on their website or social media). Log each school as a new init_call lead with notes on why you selected it.',
 'Submitted list of 10 schools with init_call records created. Each record has a company name, school type, estimated student count, source of selection, and at least one STEM interest signal noted in the comments field. CM review confirms leads are genuinely new and in the right territory.',
 'https://stemlearning.in/resources/prospecting-guide-v1.pdf',
 'stem_coach_rubrics/drills/pros_beg_01_rubric.json',
 20, 1),

('PROS_ADV_01',
 'PROSPECTING',
 'role_play',
 'advanced',
 'Mine 3 referrals from a recently won school',
 'You have just won a new STEM lab deal with a school. Your task: contact the principal within 5 days of the contract signing and ask for introductions to 3 other school principals in their network (same cluster or trust group). Draft your referral ask message (WhatsApp or call script), send it, and log the referral conversation as a tblcallevents entry. Target: at least 1 of the 3 referrals results in a new init_call lead within 14 days.',
 'Referral ask message is logged in the CRM. At least 1 new init_call lead created from the referral chain within 14 days. The lead record shows the referral source school in the notes. Coach AI scores the referral ask message on: opening, personal connection, specific request, and ease of introduction (rubric max 12 points, pass at 8).',
 'https://stemlearning.in/resources/referral-ask-script-v2.pdf',
 'stem_coach_rubrics/drills/pros_adv_01_rubric.json',
 15, 1),

-- ============================================================================
-- SKILL: DISCOVERY
-- ============================================================================
('DISC_BEG_01',
 'DISCOVERY',
 'script',
 'beginner',
 'Write the 5 mandatory discovery questions for your next school call',
 'Before your next new lead call (cstatus 1 or 2), prepare a written list of the 5 mandatory discovery questions as specified in the Discovery Rubric: (1) What is the school\'s current approach to STEM education? (2) What is the primary challenge you face with student engagement in science and technology? (3) What is the approximate budget cycle and decision-making timeline? (4) Who else in the leadership team would be involved in evaluating a STEM lab proposal? (5) What does success look like for you in year one of a STEM lab? Submit the prepared question list to your coach before the call.',
 'Question list submitted before the call. After the call, the BD logs the answers to each discovery question in the tblcallevents notes. Coach AI evaluates the notes for completeness: 5/5 questions answered scores A+, 4/5 scores A, 3/5 scores B. Below 3/5 is a gap signal.',
 'https://stemlearning.in/resources/discovery-question-guide.pdf',
 'stem_coach_rubrics/drills/disc_beg_01_rubric.json',
 10, 1),

('DISC_ADV_01',
 'DISCOVERY',
 'role_play',
 'advanced',
 'Run a 20-minute recorded discovery call using the 5-question framework',
 'Conduct a live or practice discovery call (with a real prospect or a peer playing the role of a school principal) using the 5-question framework. Record the call (with consent if real). Upload the recording or transcript to the Asset Review module in the CRM. The coach will evaluate: question sequencing, active listening indicators (paraphrasing, follow-up questions), and whether the BD captured pain points that can anchor the pitch.',
 'Recording or transcript uploaded within 24 hours of the call. Coach AI scores: question coverage (0 to 10), listening quality (0 to 10), pain point capture (0 to 5). Total pass mark: 18 of 25. BD must also log a post-call debrief note in the lead record.',
 NULL,
 'stem_coach_rubrics/drills/disc_adv_01_rubric.json',
 25, 1),

-- ============================================================================
-- SKILL: COLD_CALL_OPENING
-- ============================================================================
('COLD_BEG_01',
 'COLD_CALL_OPENING',
 'script',
 'beginner',
 'Memorise and deliver the STEM approved cold-call opener (90 seconds)',
 'Study the STEM approved cold-call opener template (attached below). Record yourself delivering it in under 90 seconds. Submit the audio or text to the Asset Review module. The opener must include: (1) your name and STEM Learning introduction, (2) a school-specific hook (reference the school by name and cite one observable fact about them), (3) a single clear ask (a 15-minute discovery call, not a demo). Prompt: "Good morning, I am calling from STEM Learning. I noticed that [school name] recently [specific observation, e.g. won a science fair / launched a new block / enrolled in PMSHRI]. We work with schools like yours to set up [lab type] labs that help students [outcome]. I would love 15 minutes to understand your current setup. Would next Tuesday or Thursday morning work for you?"',
 'Recording or written delivery submitted. Coach AI scores on: brevity (under 90 seconds), school-specific hook (present or absent), clear single ask (present or absent), professional tone (0 to 3). Pass: 7 of 9 points.',
 'https://stemlearning.in/resources/cold-call-opener-template.pdf',
 'stem_coach_rubrics/drills/cold_beg_01_rubric.json',
 10, 1),

('COLD_ADV_01',
 'COLD_CALL_OPENING',
 'peer_watch',
 'advanced',
 'Shadow a senior BD\'s cold call and write a debrief',
 'Arrange to listen in (with permission) on a cold call or first-touch call made by a senior BD in your cluster who has a track record of converting cstatus 1 leads to cstatus 2 within 7 days. After the call, write a 200-word debrief: (1) What hook did the senior BD use? (2) How did they earn the next meeting? (3) What would you do differently? Submit the debrief in the CRM coaching log.',
 'Debrief submitted within 24 hours of the shadow call. The debrief references 3 specific moments from the call. Coach AI evaluates the debrief for analytical depth and self-awareness. CM counter-signs as confirmation the shadow call happened.',
 NULL,
 'stem_coach_rubrics/drills/cold_adv_01_rubric.json',
 30, 1),

-- ============================================================================
-- SKILL: PITCHING
-- ============================================================================
('PITCH_BEG_01',
 'PITCHING',
 'script',
 'beginner',
 'Write a 3-minute pitch script anchored on the school\'s named pain',
 'Select one of your cstatus 2 or 3 leads. Using the discovery notes on file, write a 3-minute pitch script that: (1) opens by naming the school\'s top pain point (from discovery), (2) introduces the relevant STEM lab configuration, (3) describes one measurable student outcome (not a feature list), (4) pre-empts the top objection (typically price or space), and (5) closes with a named next step (a demo, a site visit, or a reference school visit). Submit the script to Asset Review in the CRM.',
 'Script submitted and reviewed by coach AI. Pass criteria: pain point named (yes/no), lab configuration matched to pain (yes/no), outcome quantified (yes/no), objection pre-empted (yes/no), next step named with date (yes/no). Pass: 4 of 5 criteria met. Grade B or above required.',
 'https://stemlearning.in/resources/pitch-script-template-v3.pdf',
 'stem_coach_rubrics/drills/pitch_beg_01_rubric.json',
 20, 1),

('PITCH_ADV_01',
 'PITCHING',
 'role_play',
 'advanced',
 'Deliver a live pitch to a CM playing the role of a sceptical principal',
 'Schedule a 20-minute pitch role-play session with your CM. The CM will play a sceptical school principal (Tier 2 city, 500-student school, no prior STEM lab experience, tight budget). Deliver your full pitch including demo walkthrough, objection handling, and close. The CM scores you on the Pitching Rubric (5 criteria, max 15 points) immediately after. Record the session if both parties consent. Log the session in the CRM coaching assignment.',
 'Role-play conducted and logged in CRM within the assigned week. CM submits their rubric score (out of 15). Pass: 10 or above. If below 10, a second role-play is scheduled within 5 days. BD writes a self-improvement note citing 3 specific changes they will make.',
 NULL,
 'stem_coach_rubrics/drills/pitch_adv_01_rubric.json',
 25, 1),

-- ============================================================================
-- SKILL: OBJECTION_HANDLING
-- ============================================================================
('OBJH_BEG_01',
 'OBJECTION_HANDLING',
 'script',
 'beginner',
 'Prepare written responses to the 5 most common school objections',
 'The top 5 objections heard in the field are: (1) Your price is too high. (2) We do not have space for a dedicated lab. (3) Our teachers will not know how to use it. (4) We are already working with another vendor. (5) We need approval from the management trust. Write a 3-to-5 sentence response for each objection using the STEM approved response framework: Acknowledge, Reframe, Evidence, Next Step (AREN). Submit the 5 responses via the CRM coaching log.',
 'All 5 responses submitted. Coach AI evaluates each on the AREN framework: Acknowledge (0-1), Reframe (0-1), Evidence cited (0-1), Next Step proposed (0-1). Pass: 3 out of 4 on at least 4 of the 5 objections.',
 'https://stemlearning.in/resources/objection-handling-guide-v2.pdf',
 'stem_coach_rubrics/drills/objh_beg_01_rubric.json',
 15, 1),

('OBJH_ADV_01',
 'OBJECTION_HANDLING',
 'role_play',
 'advanced',
 'Survive a 10-minute objection gauntlet with your CM',
 'Your CM will throw 8 objections at you in rapid succession (including 2 you have not prepared for) during a timed 10-minute role-play. You must respond to each without losing your composure, conceding price unprompted, or naming a competitor. After the gauntlet, the CM rates each response (0 to 2 per objection, max 16). You then self-rate your composure and adaptability (1 to 5). Submit the session log to the CRM.',
 'Session logged in CRM. CM score 10 or above out of 16 to pass. If a response required conceding price without trading value, that objection is flagged as a critical gap signal. BD writes a 100-word debrief on the 2 hardest objections.',
 NULL,
 'stem_coach_rubrics/drills/objh_adv_01_rubric.json',
 15, 1),

-- ============================================================================
-- SKILL: DM_MAPPING
-- ============================================================================
('DMMP_BEG_01',
 'DM_MAPPING',
 'script',
 'beginner',
 'Complete the stakeholder map for one of your cstatus 3 leads',
 'For one of your Tentative (cstatus 3) leads, complete the STEM Stakeholder Map template: identify and document (1) Decision Maker (DM): the person who can say yes, (2) Final Buyer (FB): the person who signs the cheque or approves the purchase order, (3) CSR Sponsor (if applicable): the corporate or government entity funding the lab, (4) Influencer: a teacher, HOD, or trust member who can support or block the deal, (5) Gatekeeper: the person who controls access to the DM. Log the map in the lead record under the Stakeholder tab.',
 'Stakeholder map submitted for the selected lead. All 5 roles attempted (some may be TBD if not yet identified). Coach AI validates that DM name, designation, and contact are present. A missing DM name at cstatus 3 is a gap signal. CM reviews and confirms accuracy.',
 'https://stemlearning.in/resources/stakeholder-map-template.pdf',
 'stem_coach_rubrics/drills/dmmp_beg_01_rubric.json',
 20, 1),

('DMMP_ADV_01',
 'DM_MAPPING',
 'role_play',
 'advanced',
 'Navigate gatekeeping to reach the real decision maker in a role-play',
 'CM plays a school receptionist or vice-principal who is blocking access to the principal. Your task: over 15 minutes of role-play, use your stakeholder mapping skills to identify who the real decision maker is, build rapport with the gatekeeper, and earn an introduction to the DM. You may not mention price. After the role-play, update the lead stakeholder map with what you learned.',
 'Role-play logged in CRM. CM rates the BD on: rapport building (0-3), information extraction without pressure (0-3), escalation path identified (0-3). Pass: 6 of 9. Stakeholder map updated in the CRM within 2 hours of the drill.',
 NULL,
 'stem_coach_rubrics/drills/dmmp_adv_01_rubric.json',
 20, 1),

-- ============================================================================
-- SKILL: PROPOSAL_WRITING
-- ============================================================================
('PROP_BEG_01',
 'PROPOSAL_WRITING',
 'script',
 'beginner',
 'Write a one-page executive summary for a cstatus 6 lead using the STEM proposal template',
 'Download the STEM Proposal Template (STEM Learner Portal > Resources > Proposal Templates). For one of your Positive (cstatus 6) leads, complete the executive summary section: school name and context (2 sentences), the primary pain being addressed (2 sentences), the proposed STEM lab solution (3 sentences with lab type and configuration), key outcomes promised (3 bullet points), and pricing overview (use the approved pricing sheet, not indicative figures). Submit to Asset Review in the CRM for coach review.',
 'Executive summary submitted via Asset Review. Coach AI evaluates: template used (yes/no), school-specific pain cited (yes/no), lab type matched to pain (yes/no), 3 outcomes quantified (0-3), pricing from approved sheet (yes/no). Pass: grade B or above (4 of 5 criteria met). BDs must address all coach feedback before submitting the full proposal.',
 'https://stemlearning.in/resources/proposal-template-fy27.pdf',
 'stem_coach_rubrics/drills/prop_beg_01_rubric.json',
 30, 1),

('PROP_ADV_01',
 'PROPOSAL_WRITING',
 'script',
 'advanced',
 'Write a full proposal for a Rs 15 lakh deal and get it coach-approved',
 'For one of your cstatus 6 or 7 leads where the deal value is estimated above Rs 15 lakh, write a complete proposal using the STEM Proposal Template: executive summary, school context and pain, proposed solution and lab configuration, outcome measurement plan, pricing breakdown (hardware, curriculum, training, AMC), implementation timeline, and reference school data. Submit for coach AI review and achieve a grade of A or A+. Then get CM sign-off before sending to the school.',
 'Full proposal submitted via Asset Review. Coach AI grade A or A+ achieved. CM sign-off logged in the CRM. Proposal sent to the school via STEM Secure Share (logged in the CRM). If the first submission scores below A, the BD must revise and resubmit within 24 hours addressing all coach feedback.',
 'https://stemlearning.in/resources/proposal-template-fy27.pdf',
 'stem_coach_rubrics/drills/prop_adv_01_rubric.json',
 60, 1),

-- ============================================================================
-- SKILL: FOLLOWUP_CADENCE
-- ============================================================================
('FOLU_BEG_01',
 'FOLLOWUP_CADENCE',
 'script',
 'beginner',
 'Plan a 3-touch follow-up sequence for a proposal-sent lead',
 'For one of your cstatus 7 (Proposal Sent) leads, draft a 3-touch follow-up sequence: (1) Touch 1 (Day 3 after proposal send): a WhatsApp message confirming receipt, asking for initial reactions, and sharing one reference school story. (2) Touch 2 (Day 7): a brief call or voice note sharing a relevant case study or new product update. (3) Touch 3 (Day 12): a soft-close message asking for a meeting to discuss questions and agree on a next step. Write all 3 messages and submit them in the coaching log.',
 'All 3 messages written and submitted. Coach AI evaluates each on: clarity, value add (reference or case study included), and soft-close element. Pass: at least 2 of 3 messages score B or above. The BD must then execute the sequence in the real lead and log each touch as a tblcallevents event.',
 'https://stemlearning.in/resources/followup-cadence-guide.pdf',
 'stem_coach_rubrics/drills/folu_beg_01_rubric.json',
 20, 1),

('FOLU_ADV_01',
 'FOLLOWUP_CADENCE',
 'role_play',
 'advanced',
 'Execute a live 3-touch cadence on a real cstatus 7 lead and debrief',
 'Select a real cstatus 7 lead where no follow-up has been logged in the past 5 days. Execute the full 3-touch cadence (over 12 days): WhatsApp Day 3, call Day 7, soft-close Day 12. Log each touch in the CRM. After the 3rd touch, write a 200-word debrief on: school reaction to each touch, what worked, what you would change, and the current status of the lead. If the lead advances to cstatus 8 or above within 14 days, that is a positive outcome signal.',
 'All 3 tblcallevents entries logged with purpose codes. Debrief submitted within 24 hours of Touch 3. Coach AI reads the debrief for analytical depth. Lead status checked at day 14: advancement to cstatus 8+ = A signal, no change = B, lead lost = triggers a gap analysis.',
 NULL,
 'stem_coach_rubrics/drills/folu_adv_01_rubric.json',
 45, 1),

-- ============================================================================
-- SKILL: NEGOTIATION
-- ============================================================================
('NEGO_BEG_01',
 'NEGOTIATION',
 'script',
 'beginner',
 'Identify 3 value-adds you can trade instead of discounting price',
 'Price pressure is the most common negotiation challenge. For your current pipeline, identify 3 specific value-adds that STEM Learning can offer in negotiation WITHOUT reducing the hardware price: examples include extended training sessions, an additional teacher orientation day, staggered payment schedule, an on-site maintenance visit added to the first AMC year, or co-branded lab signage. Write a justification for each value-add (why this school would value it, what it costs STEM Learning, and at what point you would offer it). Submit via the coaching log.',
 '3 value-adds submitted with justifications. Coach AI evaluates: relevance to the specific school (is it tailored or generic?), STEM cost awareness (does the BD know the approximate cost of the add?), and trade-readiness (is there a clear trigger for when to offer it?). Pass: 2 of 3 value-adds score well on all 3 dimensions.',
 'https://stemlearning.in/resources/negotiation-value-add-guide.pdf',
 'stem_coach_rubrics/drills/nego_beg_01_rubric.json',
 15, 1),

('NEGO_ADV_01',
 'NEGOTIATION',
 'role_play',
 'advanced',
 'Survive a price-pressure negotiation role-play without conceding below the floor',
 'CM plays a school trustee who opens with a demand for a 25 percent discount and signals they have a competing quote 20 percent lower. Your task: over 20 minutes, hold the price fence, trade value instead of price, document the trade in a mock MoM, and close with a conditional yes (if the management approves by Friday, STEM will include the free teacher refresher day). You may offer up to 8 percent maximum per the approved discount matrix. Going below 8 percent or conceding without a trade is a critical gap signal.',
 'Role-play logged in CRM. CM scores on Negotiation Rubric (5 criteria, max 15). Pass: 10 or above. If the BD went below 8 percent without trading value, the drill is marked failed and rescheduled. BD writes a mock MoM of the negotiation outcome within 1 hour.',
 NULL,
 'stem_coach_rubrics/drills/nego_adv_01_rubric.json',
 25, 1),

-- ============================================================================
-- SKILL: CLOSING
-- ============================================================================
('CLOS_BEG_01',
 'CLOSING',
 'script',
 'beginner',
 'Write 3 closing questions using the if-then framework',
 'The if-then close is the most reliable closing technique for school deals: "If the management committee approves this by [date], can we target [specific outcome] together?" Write 3 versions of the if-then close tailored to: (1) a school in cstatus 8 where the DM is identified but the final buyer is the management trustee, (2) a school in cstatus 9 where price terms are being debated, and (3) a school approaching year-end budget deadline. Each close should be 2 to 3 sentences. Submit via the coaching log.',
 'All 3 closing questions submitted. Coach AI evaluates: if-then structure present (yes/no), specific date mentioned (yes/no), named next step or outcome included (yes/no). Pass: 2 of 3 criteria met on all 3 versions.',
 'https://stemlearning.in/resources/closing-techniques-guide.pdf',
 'stem_coach_rubrics/drills/clos_beg_01_rubric.json',
 15, 1),

('CLOS_ADV_01',
 'CLOSING',
 'role_play',
 'advanced',
 'Close a real or simulated Very Positive lead in a 15-minute role-play',
 'CM plays a school principal in cstatus 9 (Very Positive): they like the product, the price is agreed in principle, but they are "thinking about it" and have not signed. Your task: in 15 minutes, ask for the order explicitly using the if-then framework, handle 2 final hesitations from the CM, and leave the conversation with either a signed commitment or a specific date for the final decision. After the role-play, draft the post-meeting WhatsApp message confirming the commitment.',
 'Role-play logged in CRM. CM scores on Closing Rubric (5 criteria, max 15). Pass: 11 or above. Draft post-meeting WhatsApp submitted for coach review within 1 hour. If the BD failed to ask for the order explicitly, that is a critical gap signal.',
 NULL,
 'stem_coach_rubrics/drills/clos_adv_01_rubric.json',
 20, 1),

-- ============================================================================
-- SKILL: STAKEHOLDER_MANAGEMENT
-- ============================================================================
('STKH_BEG_01',
 'STAKEHOLDER_MANAGEMENT',
 'script',
 'beginner',
 'Map the alignment level of each stakeholder for a cstatus 8 lead',
 'For one of your Open RPEM (cstatus 8) leads, rate each identified stakeholder on alignment: (1 = opposing, 2 = neutral, 3 = supportive, 4 = actively championing). For each stakeholder rated 1 or 2, write a 2-sentence plan for how you will move them to at least 3. Submit the alignment map and plans in the CRM coaching log.',
 'Alignment map submitted. All stakeholders in the stakeholder map rated. At least 1 plan for each low-alignment stakeholder. Coach AI checks: is the plan specific to the stakeholder (not generic), does it include a named action (not just "build rapport"), and is there a timeline? Pass: 2 of 3 checks for each plan.',
 'https://stemlearning.in/resources/stakeholder-alignment-guide.pdf',
 'stem_coach_rubrics/drills/stkh_beg_01_rubric.json',
 20, 1),

('STKH_ADV_01',
 'STAKEHOLDER_MANAGEMENT',
 'peer_watch',
 'advanced',
 'Attend a multi-stakeholder school meeting and write a cross-stakeholder debrief',
 'Attend a school meeting (real or accompanied by senior BD or CM) where both the principal and at least one management trustee or CSR sponsor are present. Observe how the senior BD navigates the different stakeholders simultaneously: who they address on each topic, how they manage conflicting signals from different stakeholders, and how they keep momentum toward a next step. Write a 300-word debrief: what worked, what you would add, and how you will apply one technique in your next multi-stakeholder meeting.',
 'Debrief submitted within 24 hours. The debrief cites 3 specific stakeholder management moments from the meeting. Coach AI evaluates for depth and actionable takeaway. Peer or CM confirms the BD was present at the meeting.',
 NULL,
 'stem_coach_rubrics/drills/stkh_adv_01_rubric.json',
 45, 1),

-- ============================================================================
-- SKILL: REFERRALS
-- ============================================================================
('REFE_BEG_01',
 'REFERRALS',
 'script',
 'beginner',
 'Draft a referral ask message for a recently won school',
 'Using the Referral Ask Template (STEM Learner Portal > Resources), draft a WhatsApp message to the principal of your most recently won school (cstatus 12). The message should: (1) thank the principal for the partnership, (2) mention one positive outcome already observed (even early signs), (3) ask for an introduction to 2 other school principals the principal knows, and (4) make it easy to say yes (offer to draft the intro message for them). Submit the draft for coach review.',
 'Draft submitted via Asset Review. Coach AI evaluates on 4 criteria (as above). Pass: grade B or above. BD must send the actual message within 3 days of the drill and log the send in the CRM.',
 'https://stemlearning.in/resources/referral-ask-template.pdf',
 'stem_coach_rubrics/drills/refe_beg_01_rubric.json',
 15, 1),

('REFE_ADV_01',
 'REFERRALS',
 'script',
 'advanced',
 'Build a reference story from a Won account and use it in a live pitch',
 'Interview the principal or a teacher at one of your Won schools (3 to 5 questions about student engagement, teacher confidence, and measurable outcomes since lab install). Write a 3-paragraph reference story: (1) school context before the lab, (2) implementation experience, (3) observable impact on students. Get the principal to approve the story for use. Then use the story in a real pitch to a cstatus 3 or 6 lead and log the pitch in the CRM.',
 'Reference story written and principal approval documented. Story used in a real pitch with the lead CRM entry referencing the story. Coach AI evaluates the story for: specific outcomes mentioned (yes/no), principal quoted or cited (yes/no), and applicability to new school context (yes/no). Pass: all 3 criteria met.',
 NULL,
 'stem_coach_rubrics/drills/refe_adv_01_rubric.json',
 40, 1),

-- ============================================================================
-- SKILL: ACCOUNT_EXPANSION
-- ============================================================================
('ACEX_BEG_01',
 'ACCOUNT_EXPANSION',
 'script',
 'beginner',
 'Identify the upsell lane for one of your Won accounts',
 'For one of your cstatus 12 (Won) accounts, review the contract and the school\'s profile. Identify 3 potential upsell opportunities: examples include a second lab of a different type, an AMC renewal with upgrade, a teacher certification add-on, a student competition package, or an expanded student licence for an additional grade level. For each opportunity, write: what it is, why this school would value it, and when to raise it (timing relative to current contract). Submit via the coaching log.',
 '3 upsell opportunities submitted. Coach AI evaluates: each opportunity is specific to the school (not generic), timing rationale is sound, and STEM Learning has a product that matches. Pass: 2 of 3 opportunities well-justified. BD must tag the best opportunity in the CRM lead record under Upsell Intent.',
 'https://stemlearning.in/resources/account-expansion-guide.pdf',
 'stem_coach_rubrics/drills/acex_beg_01_rubric.json',
 20, 1),

('ACEX_ADV_01',
 'ACCOUNT_EXPANSION',
 'role_play',
 'advanced',
 'Seed an upsell conversation with a Won school principal in a 15-minute call',
 'Call the principal of a Won school that is at least 3 months post-installation. Open with a relationship check-in (2 minutes). Then transition to seeding the upsell: share one piece of content (new product brochure, competition programme, or government scheme) relevant to the school, gauge their reaction, and leave with a specific next step (a follow-up call or a meeting with the RM). Log the call as a tblcallevents entry with the upsell intent flagged.',
 'Call logged in CRM with tblcallevents entry. Upsell intent tagged in the lead record. Coach AI reads the call notes for: relationship check-in present (yes/no), content or value shared (yes/no), specific next step agreed (yes/no). Pass: all 3 criteria met. If no next step agreed, the BD must schedule a follow-up within 5 days.',
 NULL,
 'stem_coach_rubrics/drills/acex_adv_01_rubric.json',
 20, 1),

-- ============================================================================
-- SKILL: PRESENTATION
-- ============================================================================
('PRES_BEG_01',
 'PRESENTATION',
 'script',
 'beginner',
 'Structure a 10-minute school board presentation outline',
 'You have been asked to present the STEM lab proposal to a school management committee (principal + 2 trustees) in 10 minutes. Write the outline: slide 1 title and hook (30 seconds), slide 2 school context and pain (1 minute), slide 3 proposed solution with lab visual (2 minutes), slide 4 outcomes and evidence from 2 reference schools (2 minutes), slide 5 investment and ROI summary (2 minutes), slide 6 next steps and ask (1 minute), wrap-up (30 seconds). Submit the outline with key talking points per slide.',
 'Outline submitted with talking points. Coach AI evaluates: structure follows the 10-minute window (yes/no), opening hook is school-specific (yes/no), outcomes are quantified (yes/no), ask is explicit (yes/no). Pass: 3 of 4 criteria met.',
 'https://stemlearning.in/resources/board-presentation-guide.pdf',
 'stem_coach_rubrics/drills/pres_beg_01_rubric.json',
 20, 1),

('PRES_ADV_01',
 'PRESENTATION',
 'role_play',
 'advanced',
 'Deliver a 10-minute board presentation to your CM and an observer',
 'Deliver the 10-minute board presentation (using the outline from PRES_BEG_01 or a new school\'s deck) to your CM and one observer (peer BD or RM). The CM plays a sceptical trustee who asks 3 unscripted questions during the presentation. Observer times the presentation and notes any over-runs. After the presentation, CM and observer each fill out the Presentation Rubric (5 criteria, max 15). BD self-rates and submits all scores to the CRM.',
 'Presentation delivered and all scores submitted in CRM. Combined CM and observer score 20 or above out of 30 to pass. Any slide section that runs more than 30 seconds over time is a time-management flag. BD writes a 100-word post-presentation improvement plan.',
 NULL,
 'stem_coach_rubrics/drills/pres_adv_01_rubric.json',
 20, 1),

-- ============================================================================
-- SKILL: ACTIVE_LISTENING
-- ============================================================================
('LIST_BEG_01',
 'ACTIVE_LISTENING',
 'script',
 'beginner',
 'Paraphrase 5 school contact statements from a recent call recording',
 'Listen to a recording of one of your recent school calls (or a practice call provided by your CM). Identify 5 moments where the school contact expressed a concern, preference, or need. For each moment, write the exact quote (or close paraphrase) and then write a 1-to-2 sentence active listening response you should have given or did give: acknowledge the statement, reflect the emotion or concern, and invite them to say more. Submit the 5 pairs (statement + response) via the coaching log.',
 '5 pairs submitted. Coach AI evaluates each response for: acknowledgement (yes/no), emotion or concern reflected (yes/no), invitation to continue (yes/no). Pass: 2 of 3 elements present in at least 4 of the 5 responses.',
 'https://stemlearning.in/resources/active-listening-guide.pdf',
 'stem_coach_rubrics/drills/list_beg_01_rubric.json',
 15, 1),

('LIST_ADV_01',
 'ACTIVE_LISTENING',
 'peer_watch',
 'advanced',
 'Evaluate your own discovery call recording against the Active Listening Rubric',
 'Record your next discovery or pitch call (with the school contact\'s consent). Upload the recording to Asset Review (audio transcript option). The coach will transcribe and analyse the call for active listening indicators: number of times you paraphrased a contact statement, number of clarifying follow-up questions asked, instances where you interrupted the contact, and moments where you pivoted to a sales point before the contact finished speaking. Review the coach analysis and write a 200-word action plan for your next call.',
 'Recording or transcript uploaded. Coach AI returns a listening quality score (0 to 20). Pass: 14 or above. Interruptions flagged. BD submits a 200-word action plan within 24 hours of receiving the coach analysis.',
 NULL,
 'stem_coach_rubrics/drills/list_adv_01_rubric.json',
 30, 1);

-- ============================================================================
-- END OF SEED: coaching_drill (28 rows = 2 drills x 14 skills)
-- ============================================================================
