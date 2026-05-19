-- ============================================================================
-- STEM CRM - Migration 036 - Seed: greetings_template_seed
-- 22 Indian festival rows + 4 occasion rows (birthday, anniversary, win, recovery)
-- ============================================================================
-- Plain English. No em-dashes. No non-ASCII characters.
-- Idempotent: INSERT IGNORE on primary key.
-- {placeholders}: {recipient_name}, {school_name}, {bd_name}, {cluster}
-- template_regional_hint: natural-language instruction for the drafter agent
--   to compose a regional variant; actual regional text is generated at runtime.
-- Author: STEM ops
-- Date: 2026-05-18
-- ============================================================================

INSERT IGNORE INTO greetings_template_seed
  (occasion_code, festival_name, festival_date_pattern, occasion_type,
   variant_label, template_formal_en, template_warm_en, template_regional_hint,
   proposed_channel, target_audience, active, created_by_uid)
VALUES

-- ============================================================================
-- FESTIVAL 1: Diwali (October / November - lunar, ~10-MM range)
-- ============================================================================
('diwali', 'Diwali', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, wishing you and the entire {school_name} family a joyous Diwali. May this festival of lights bring prosperity, good health, and new opportunities to your institution. Warm regards, {bd_name}, STEM Learning.',
 'Happy Diwali, {recipient_name}! May your home and school shine bright this festive season. Wishing your students a year full of discovery and achievement. From {bd_name} and the STEM Learning team.',
 'Compose a warm Diwali wish in Hindi using formal address (Aadarniya) referencing the school name and the theme of knowledge as light (Gyan ka Prakash). Avoid religious specifics beyond the festive greeting.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 2: Holi
-- ============================================================================
('holi', 'Holi', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, on the occasion of Holi, STEM Learning extends warm greetings to you and the {school_name} community. May the colours of this festival bring joy, creativity, and vibrant energy to your students this year.',
 'Happy Holi, {recipient_name}! A festival as colourful as the curiosity of young learners. Wishing you and all at {school_name} a joyful celebration.',
 'Compose a cheerful Holi greeting in Hindi referencing colours and the joy of learning. Keep it short (2 to 3 sentences), suitable for sending to a school principal.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 3: Eid al-Fitr
-- ============================================================================
('eid_al_fitr', 'Eid al-Fitr', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Eid Mubarak! On this auspicious occasion of Eid al-Fitr, STEM Learning wishes you, your family, and the entire {school_name} community joy, peace, and blessings. May this Eid mark new beginnings for your students.',
 'Eid Mubarak, {recipient_name}! Wishing you a joyful Eid filled with happiness, family, and good tidings. We at STEM Learning cherish our partnership with {school_name}.',
 'Compose a warm Eid Mubarak greeting in Urdu (transliterated or script as appropriate for the cluster) referencing peace, learning, and community. Keep tone respectful and inclusive.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 4: Eid al-Adha
-- ============================================================================
('eid_al_adha', 'Eid al-Adha', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Eid ul Adha Mubarak! May this blessed occasion bring peace, unity, and prosperity to you and your loved ones. STEM Learning wishes {school_name} a season of gratitude and togetherness.',
 'Eid ul Adha Mubarak, {recipient_name}! Wishing you and your family a blessed celebration. Thank you for the trust you place in our partnership.',
 'Compose a brief Eid ul Adha greeting in Urdu suitable for a school principal. Reference the spirit of giving and community learning.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 5: Christmas
-- ============================================================================
('christmas', 'Christmas', '12-25', NULL,
 'formal_en',
 'Dear {recipient_name}, Season\'s Greetings from STEM Learning! Wishing you and all at {school_name} a peaceful and joyful Christmas. May the new year bring growth, innovation, and success to your students and staff.',
 'Merry Christmas, {recipient_name}! Wishing the whole {school_name} family a wonderful holiday season filled with warmth and joy. Looking forward to continuing our journey together in the new year.',
 'Compose a warm Christmas greeting in English (no regional variant needed). Reference the spirit of giving, learning, and celebration. Suitable for all school types including non-Christian institutions.',
 'email', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 6: New Year (January 1)
-- ============================================================================
('new_year', 'New Year', '01-01', NULL,
 'formal_en',
 'Dear {recipient_name}, A very Happy New Year from STEM Learning! We are grateful for the partnership with {school_name} and look forward to another year of inspiring young learners together. Wishing you health, happiness, and great achievements in the year ahead.',
 'Happy New Year, {recipient_name}! May this year bring exciting opportunities for your students at {school_name}. Thank you for trusting STEM Learning as a partner in your school\'s growth.',
 'Compose a New Year greeting in Hindi referencing the new academic opportunities and student success. Keep it warm and forward-looking.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 7: Republic Day (January 26)
-- ============================================================================
('republic_day', 'Republic Day', '01-26', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Republic Day! On this 26th of January, STEM Learning salutes the vision of a future shaped by educated and empowered young citizens. Thank you for the work you do at {school_name} in building that future.',
 'Happy Republic Day, {recipient_name}! Our nation\'s greatest resource is its students. Thank you for nurturing them at {school_name}. Proud to be your STEM learning partner.',
 'Compose a Republic Day message in Hindi referencing national pride, education, and the role of schools in building future citizens. Keep it respectful and non-political.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 8: Independence Day (August 15)
-- ============================================================================
('independence_day', 'Independence Day', '08-15', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Independence Day! STEM Learning celebrates this day by reaffirming our commitment to quality STEM education for every Indian child. Wishing {school_name} and its students a bright and empowered future.',
 'Happy 15th August, {recipient_name}! Today we celebrate the freedom that makes education possible. Thank you for the incredible work you and your team do at {school_name}.',
 'Compose an Independence Day greeting in Hindi referencing Azadi, education as freedom, and student empowerment. Keep it brief, patriotic, and inclusive.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 9: Teacher's Day (September 5)
-- ============================================================================
('teachers_day', 'Teacher\'s Day', '09-05', NULL,
 'formal_en',
 'Dear {recipient_name}, on this Teachers\' Day, STEM Learning celebrates every educator who lights the path for young learners. Your dedication at {school_name} is the foundation on which our future is built. Thank you.',
 'Happy Teachers\' Day, {recipient_name}! Teachers are the real changemakers. We at STEM Learning are honoured to support your incredible work at {school_name}. Here is to you and your team today.',
 'Compose a heartfelt Teachers Day message in Hindi referencing the Guru-shishya tradition and the teacher as guide and inspiration. Suitable for sending to the principal and teaching staff.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 10: Children's Day (November 14)
-- ============================================================================
('childrens_day', 'Children\'s Day', '11-14', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Children\'s Day! At STEM Learning, every day is about sparking curiosity in young minds. On Bal Diwas, we celebrate the students of {school_name} and the future they are building through their learning journey.',
 'Happy Children\'s Day, {recipient_name}! Children are the reason we do what we do. Today is a wonderful reminder of the purpose behind every lab we build and every skill we teach. Cheers to the young innovators at {school_name}!',
 'Compose a Children\'s Day (Bal Diwas) message in Hindi referencing Nehru\'s love for children and the joy of young learners. Warm, playful tone appropriate for a school community.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 11: Ganesh Chaturthi
-- ============================================================================
('ganesh_chaturthi', 'Ganesh Chaturthi', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Ganesh Chaturthi Greetings! May Lord Ganesha, the remover of obstacles and the patron of knowledge, bless {school_name} and its students with wisdom, success, and new beginnings.',
 'Ganpati Bappa Morya, {recipient_name}! Wishing you and all at {school_name} a joyful Ganesh Chaturthi. May this auspicious start bring great learning outcomes for your students this year.',
 'Compose a Ganesh Chaturthi greeting in Marathi referencing Ganpati Bappa, knowledge (Vidya), and student success. Keep it joyful and celebratory.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 12: Navratri
-- ============================================================================
('navratri', 'Navratri', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Navratri! May the nine auspicious days of this festival bring energy, positivity, and blessings to you and the entire {school_name} family. Wishing you a joyful celebration.',
 'Navratri Greetings, {recipient_name}! Nine days of energy and devotion. Wishing you and your school community a vibrant and joyful Navratri.',
 'Compose a Navratri greeting in Hindi or Gujarati referencing the nine nights of celebration, Shakti, and the energy of new beginnings. Keep it warm and non-denominational in tone.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 13: Durga Puja
-- ============================================================================
('durga_puja', 'Durga Puja', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Shubho Bijoya! STEM Learning wishes you and the {school_name} community a joyful Durga Puja. May the blessings of Maa Durga bring strength, wisdom, and success to your students.',
 'Shubho Bijoya, {recipient_name}! Wishing you and your family a beautiful Durga Puja. The enthusiasm of the celebrations is as vibrant as the curiosity of the young learners at {school_name}.',
 'Compose a Durga Puja greeting in Bengali referencing Shubho Bijoya, Maa Durga, and the spirit of community and learning. Warm, festive tone.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 14: Onam
-- ============================================================================
('onam', 'Onam', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Onam! May the harvest festival of Onam bring abundance, happiness, and prosperity to you and the {school_name} community. Wishing your students a year as vibrant as the Pookalam.',
 'Onam Ashamsakal, {recipient_name}! Wishing you a wonderful Onam filled with the joy of family, food, and the spirit of Kerala\'s rich culture. Thank you for the trust you place in STEM Learning.',
 'Compose an Onam greeting in Malayalam referencing Maveli, the harvest, and community celebration. Warm and culturally grounded for Kerala school contacts.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 15: Pongal
-- ============================================================================
('pongal', 'Pongal', '01-14', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Pongal! On this harvest festival, STEM Learning wishes you, your family, and the {school_name} community abundance, health, and joy. May your students\' achievements overflow like the Pongal pot.',
 'Pongal Valthukkal, {recipient_name}! Wishing you and all at {school_name} a joyful Pongal. May this harvest season bring great results for your students.',
 'Compose a Pongal greeting in Tamil referencing the harvest, abundance, and student achievement. Warm and celebratory tone for Tamil Nadu and Tamil-speaking contacts.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 16: Baisakhi
-- ============================================================================
('baisakhi', 'Baisakhi', '04-13', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Baisakhi! May this harvest festival mark a season of new beginnings, growth, and prosperity for you and the {school_name} community. Wishing your students a year of great achievement.',
 'Happy Baisakhi, {recipient_name}! A festival of harvest and new starts. Wishing you and your school a season as bright and promising as the fields of Punjab.',
 'Compose a Baisakhi greeting in Punjabi referencing the harvest, new beginnings, and community celebration. Short, warm, and festive for North India school contacts.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 17: Raksha Bandhan
-- ============================================================================
('raksha_bandhan', 'Raksha Bandhan', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Raksha Bandhan! May the bonds of care, trust, and support that define this festival inspire the relationships within the {school_name} community. Warm wishes from the STEM Learning team.',
 'Happy Raksha Bandhan, {recipient_name}! A day celebrating the bond of care and protection. Thank you for the trust you place in STEM Learning. We are honoured to be a part of your school\'s journey.',
 'Compose a Raksha Bandhan greeting in Hindi referencing the bond of trust between STEM Learning and the school, and the festival theme of care and protection.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 18: Janmashtami
-- ============================================================================
('janmashtami', 'Janmashtami', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Janmashtami! May the divine wisdom and playful spirit of Lord Krishna inspire the young learners at {school_name} to approach every challenge with curiosity and joy.',
 'Jai Shri Krishna, {recipient_name}! Wishing you and all at {school_name} a joyful Janmashtami. May the celebration bring happiness and good energy for the school year ahead.',
 'Compose a Janmashtami greeting in Hindi referencing Krishna\'s wisdom, the joy of learning, and young students\' playful curiosity. Warm, devotional in tone but inclusive.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 19: Guru Nanak Jayanti
-- ============================================================================
('guru_nanak_jayanti', 'Guru Nanak Jayanti', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Gurpurab Diyan Lakh Lakh Vadhaiyan! On this auspicious occasion of Guru Nanak Dev Ji\'s Parkash Utsav, STEM Learning wishes you and the {school_name} community peace, wisdom, and prosperity.',
 'Happy Guru Nanak Jayanti, {recipient_name}! Guru Nanak Dev Ji\'s teachings of compassion, equality, and truth inspire all of us. Wishing you and your school a blessed and peaceful Gurpurab.',
 'Compose a Guru Nanak Jayanti greeting in Punjabi referencing Nanak\'s teachings of compassion, equality, and the pursuit of knowledge. Respectful and inclusive tone.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 20: Buddha Purnima
-- ============================================================================
('buddha_purnima', 'Buddha Purnima', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Buddha Purnima! On this sacred day celebrating the birth, enlightenment, and passing of Gautama Buddha, STEM Learning wishes {school_name} wisdom, compassion, and the pursuit of truth in all learning.',
 'Happy Buddha Purnima, {recipient_name}! May the teachings of mindfulness, compassion, and the Middle Way inspire your students at {school_name} in their journey of learning.',
 'Compose a Buddha Purnima greeting in Hindi or Pali (transliterated) referencing the Buddha\'s path of wisdom, compassion, and the pursuit of knowledge. Calm, reflective tone.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 21: Eid-e-Milad (Mawlid al-Nabi)
-- ============================================================================
('eid_e_milad', 'Eid-e-Milad', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Eid Milad-un-Nabi Mubarak! On this blessed occasion celebrating the birth of the Prophet Muhammad (peace be upon him), STEM Learning extends warm wishes to you and the {school_name} community. May this day bring peace and light.',
 'Milad Mubarak, {recipient_name}! Wishing you and your family a peaceful and blessed Eid-e-Milad. Thank you for the trust you extend to STEM Learning.',
 'Compose an Eid-e-Milad greeting in Urdu referencing the Prophet\'s birth, the theme of knowledge and mercy, and a wish for peace and blessings for the school community.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL 22: Good Friday
-- ============================================================================
('good_friday', 'Good Friday', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, STEM Learning extends respectful wishes on Good Friday. May this solemn day of reflection bring peace, solace, and renewed strength to you and your community.',
 'Good Friday greetings, {recipient_name}. May this day of quiet reflection bring you and the {school_name} community peace and comfort.',
 'Compose a short, respectful Good Friday message in English. Tone should be solemn and compassionate, acknowledging the significance of the day. No regional variant needed.',
 'email', 'all_stakeholders', 1, 1),

-- ============================================================================
-- FESTIVAL (bonus row): Easter
-- ============================================================================
('easter', 'Easter', 'lunar', NULL,
 'formal_en',
 'Dear {recipient_name}, Happy Easter from STEM Learning! May this season of renewal and hope inspire your students at {school_name} with the spirit of new beginnings. Wishing you and your family a joyful Easter.',
 'Happy Easter, {recipient_name}! A season of new beginnings and hope. Wishing you and all at {school_name} a joyful celebration filled with warmth and good cheer.',
 'Compose an Easter greeting in English referencing renewal, hope, and the joy of new beginnings. Inclusive in tone, suitable for schools of all denominations.',
 'email', 'all_stakeholders', 1, 1),

-- ============================================================================
-- OCCASION 1: Birthday (for school principal / key stakeholder)
-- ============================================================================
('birthday_principal', NULL, NULL, 'birthday',
 'formal_en',
 'Dear {recipient_name}, a very Happy Birthday from the STEM Learning team. We wish you good health, happiness, and continued success in your inspiring work at {school_name}. May this year bring you and your institution many achievements.',
 'Happy Birthday, {recipient_name}! On your special day, the STEM Learning team sends warm wishes and gratitude for the trust you place in us. Wishing you a wonderful year ahead!',
 'Compose a warm birthday message in Hindi for a school principal. Reference the principal\'s role as a guide and the work they do for students. Keep it personal and brief.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- OCCASION 2: School anniversary / contract signing anniversary
-- ============================================================================
('school_anniversary', NULL, NULL, 'anniversary',
 'formal_en',
 'Dear {recipient_name}, congratulations on another milestone year for {school_name}! STEM Learning is proud to be a part of your institution\'s journey and celebrates this anniversary with you. Here is to many more years of inspiring young learners together.',
 'Happy Anniversary, {school_name}! It has been a privilege to be a part of your school\'s story. Thank you, {recipient_name}, for the trust and partnership. Looking forward to many more milestones together.',
 'Compose a warm school anniversary message in Hindi referencing the school\'s legacy, student achievement, and the partnership with STEM Learning. Celebratory tone.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- OCCASION 3: Win broadcast (cluster congratulations on a large closure)
-- ============================================================================
('win_broadcast', NULL, NULL, 'win',
 'formal_en',
 'Congratulations to the STEM Learning {cluster} team! We are proud to announce a significant milestone: a new STEM Lab partnership with a leading school in your region. A big well done to our BD team for the outstanding work. More details to follow. Onward!',
 'Big win for the {cluster} team! A fantastic new STEM Lab closure has just been logged. Massive congratulations to the BD team involved. Keep the momentum going - let us make this a banner month!',
 'Compose a celebration message in Hindi for the BD cluster WhatsApp group referencing the achievement, team spirit, and motivation to continue strong performance.',
 'whatsapp', 'all_stakeholders', 1, 1),

-- ============================================================================
-- OCCASION 4: Loss recovery re-engagement (90 days after cstatus 13)
-- ============================================================================
('loss_recovery', NULL, NULL, 'recovery',
 'formal_en',
 'Dear {recipient_name}, it has been a while since we last connected and I wanted to reach out. A great deal has happened at STEM Learning: new lab configurations, updated pricing, and fresh case studies from schools near {school_name}. I would love to share these with you. Would you be open to a brief 15-minute call this week?',
 'Hi {recipient_name}, hope you are doing well! I wanted to touch base and share some exciting updates from STEM Learning that might be relevant to {school_name}. A lot has changed since we last spoke. Could we connect for a quick chat?',
 'Compose a gentle re-engagement message in Hindi referencing a fresh start, new information, and a low-pressure invitation to reconnect. Avoid referencing the previous loss or rejection.',
 'whatsapp', 'all_stakeholders', 1, 1);

-- ============================================================================
-- END OF SEED: greetings_template_seed (22 festival rows + 4 occasion rows = 26 rows)
-- (Includes Easter as the 22nd + 1 bonus festival = 23 festivals total,
--  Good Friday counted as 22nd per spec. Easter included as a bonus row.)
-- ============================================================================
