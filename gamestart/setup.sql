-- ============================================================
--  GAME START! — Database Setup
--  Run this in phpMyAdmin or via MySQL CLI:
--  mysql -u root -p < setup.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS gamestart
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gamestart;

-- ── Tables ──────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS category (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100)    NOT NULL,
  description VARCHAR(500)    NOT NULL DEFAULT '',
  navigation  TINYINT(1)      NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS member (
  id        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  forename  VARCHAR(100)  NOT NULL,
  surname   VARCHAR(100)  NOT NULL,
  email     VARCHAR(200)  NOT NULL,
  joined    DATE          NOT NULL,
  picture   VARCHAR(200)  NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS image (
  id       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  file     VARCHAR(200)  NOT NULL,
  alt      VARCHAR(300)  NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS article (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  title       VARCHAR(200)  NOT NULL,
  summary     VARCHAR(500)  NOT NULL DEFAULT '',
  content     TEXT          NOT NULL,
  created     DATE          NOT NULL,
  category_id INT UNSIGNED  NOT NULL,
  member_id   INT UNSIGNED  NOT NULL,
  image_id    INT UNSIGNED  DEFAULT NULL,
  published   TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE,
  FOREIGN KEY (member_id)   REFERENCES member   (id) ON DELETE CASCADE,
  FOREIGN KEY (image_id)    REFERENCES image    (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed Data ────────────────────────────────────────────

INSERT INTO category (id, name, description, navigation) VALUES
(1, 'MOBA',      'Multiplayer Online Battle Arena — team strategy at its finest', 1),
(2, 'FPS',       'First-Person Shooter — precision, reflexes, and dominance',     1),
(3, 'Strategy',  'Real-time and turn-based strategy — outsmart every opponent',   1),
(4, 'Adventure', 'Open-world and narrative adventure — explore boundless worlds', 1);

INSERT INTO member (id, forename, surname, email, joined, picture) VALUES
(1, 'Nova',   'Cruz', 'nova@gamestart.gg',  '2024-01-10', 'nova.jpg'),
(2, 'Kira',   'Vex',  'kira@gamestart.gg',  '2024-01-15', 'kira.jpg'),
(3, 'Zephyr', 'Mori', 'zeph@gamestart.gg',  '2024-02-01', 'zeph.jpg');

-- Image filenames match the actual files in the images/ folder
INSERT INTO image (id, file, alt) VALUES
( 1, 'game-01.png', 'League of Legends'),
( 2, 'game-02.png', 'Dota 2'),
( 3, 'game-03.png', 'Mobile Legends'),
( 4, 'game-04.png', 'Honor of Kings'),
( 5, 'game-05.png', 'Wild Rift'),
( 6, 'game-06.png', 'Smite'),
( 7, 'game-07.png', 'Counter-Strike 2'),
( 8, 'game-08.png', 'Valorant'),
( 9, 'game-09.png', 'Apex Legends'),
(10, 'game-10.png', 'Call of Duty Warzone'),
(11, 'game-11.png', 'Overwatch 2'),
(12, 'game-12.png', 'Escape from Tarkov'),
(13, 'game-13.png', 'StarCraft II'),
(14, 'game-14.png', 'Civilization VI'),
(15, 'game-15.png', 'Age of Empires IV'),
(16, 'game-16.png', 'XCOM 2'),
(17, 'game-17.png', 'Total War: Warhammer III'),
(18, 'game-18.png', 'Teamfight Tactics'),
(19, 'game-19.png', 'The Legend of Zelda: Tears of the Kingdom'),
(20, 'game-20.png', 'Elden Ring'),
(21, 'game-21.png', 'Red Dead Redemption 2'),
(22, 'game-22.png', 'Genshin Impact'),
(23, 'game-23.png', 'Hogwarts Legacy'),
(24, 'game-24.png', 'Baldur\'s Gate 3');

INSERT INTO article (id, title, summary, content, created, category_id, member_id, image_id, published) VALUES
(1,  'League of Legends',    'The world\'s most-played MOBA with 160+ champions',
     'League of Legends is a team-based strategy game where two teams of five powerful champions face off to destroy the other\'s base. With an ever-growing roster of over 160 champions, you\'ll find the perfect match for your playstyle. A new player experience, endless champion mastery, and frequent seasonal updates keep millions of players engaged year after year.',
     '2024-01-10', 1, 1, 1, 1),

(2,  'Dota 2',               'Deep MOBA with 124 heroes and the International championship',
     'Dota 2 is a free-to-play MOBA by Valve featuring 124 heroes and one of the deepest strategic layers in competitive gaming. Known for the annual International tournament with multi-million dollar prize pools, Dota 2 demands precise coordination, map awareness, and mastery of its complex economy system. Every match is a new puzzle to solve.',
     '2024-01-12', 1, 2, 2, 1),

(3,  'Mobile Legends',       'Asia\'s top mobile MOBA with 5v5 ranked battles',
     'Mobile Legends: Bang Bang dominates the Southeast Asian mobile gaming market with lightning-fast 5v5 battles designed for touchscreen play. Its accessible controls and short match times make it ideal for mobile, while a deep roster of heroes, frequent meta shifts, and a thriving esports scene keep professional and casual players deeply invested.',
     '2024-01-15', 1, 3, 3, 1),

(4,  'Honor of Kings',       'Tencent\'s global MOBA phenomenon with 100M+ players',
     'Honor of Kings, originally launched in China as King of Glory, has become one of the most-played mobile games on the planet. Its streamlined MOBA mechanics and roster of Chinese mythology-inspired heroes have fueled massive esports leagues. Tencent\'s global expansion brought the title to new markets with localized hero designs and fresh seasonal content.',
     '2024-01-18', 1, 1, 4, 1),

(5,  'Wild Rift',             'League of Legends reimagined for mobile and console',
     'League of Legends: Wild Rift brings the iconic MOBA experience to mobile and console platforms with redesigned controls and a faster-paced 15-to-20-minute match format. Featuring a curated roster of popular champions from the PC version, Wild Rift also introduces exclusive skins and ranked systems tailored to its platform, building its own competitive identity.',
     '2024-01-20', 1, 2, 5, 1),

(6,  'Smite',                 'Third-person MOBA featuring gods from world mythologies',
     'Smite differentiates itself from the crowded MOBA market by placing the camera behind the character in a third-person perspective, making battles feel dramatically more personal. Players choose from gods drawn from Greek, Norse, Egyptian, and Hindu pantheons, among others. Its arena mode and assault format offer quicker alternatives to the classic conquest map.',
     '2024-01-22', 1, 3, 6, 1),

(7,  'Counter-Strike 2',      'The legendary tactical shooter reborn with modern graphics',
     'Counter-Strike 2 is Valve\'s complete overhaul of the iconic franchise, bringing sub-tick servers, fully overhauled smoke grenades that physically interact with bullets and explosions, and significantly improved visuals. The fundamental tactical formula of Terrorists versus Counter-Terrorists remains, but every system has been rebuilt to set the standard for the next decade of competitive FPS.',
     '2024-02-01', 2, 1, 7, 1),

(8,  'Valorant',              'Riot Games\' tactical FPS with agent abilities',
     'Valorant merges precise gunplay with ability-based characters called Agents, each bringing unique tactical tools to the battlefield. Developed by Riot Games with lessons learned from League of Legends\' esports success, Valorant rapidly built a competitive scene with 20+ agents, regular map additions, and a professional league drawing millions of viewers globally.',
     '2024-02-05', 2, 2, 8, 1),

(9,  'Apex Legends',          'Battle royale FPS with unique legend abilities',
     'Apex Legends reinvented the battle royale genre with its Legends system, giving each character a distinct kit that reshapes squad composition and tactical possibilities. Respawn Entertainment\'s shooter is celebrated for its fluid movement mechanics, intelligent ping communication system, and frequent limited-time events that keep the 60-player island warfare feeling fresh.',
     '2024-02-08', 2, 3, 9, 1),

(10, 'Call of Duty: Warzone', 'Free-to-play battle royale with massive player counts',
     'Call of Duty: Warzone brought the beloved military shooter franchise into the free-to-play battle royale space with Verdansk and subsequent massive maps. Support for up to 150 players, cross-play across all platforms, and frequent weapon balancing patches tied to the mainline Call of Duty titles make Warzone a live-service juggernaut that defines modern FPS competition.',
     '2024-02-12', 2, 1, 10, 1),

(11, 'Overwatch 2',           '5v5 hero shooter with evolving seasonal content',
     'Overwatch 2 transitioned the franchise to a free-to-play model with a shift from 6v6 to 5v5, accelerating pace and individual player impact. New heroes, reworks, and map pools are delivered through seasonal Battle Passes. With over 35 heroes spanning damage, tank, and support roles, team composition and ultimate ability timing remain the heart of every match.',
     '2024-02-15', 2, 2, 11, 1),

(12, 'Escape from Tarkov',    'Hardcore realistic FPS with deep survival mechanics',
     'Escape from Tarkov is an uncompromisingly hardcore tactical shooter from Battlestate Games set in the fictional Russian city of Tarkov. Players raid loot-rich locations against both AI Scavs and other players, with the ever-present risk of losing everything upon death. Its gear-fear tension, detailed ballistics system, and intricate flea market economy create an experience unlike any other FPS.',
     '2024-02-20', 2, 3, 12, 1),

(13, 'StarCraft II',          'The definitive sci-fi real-time strategy masterpiece',
     'StarCraft II remains the benchmark of real-time strategy. Managing three asymmetric factions — Terran, Protoss, and Zerg — across single-player campaigns and a fiercely competitive ladder, the game rewards mechanical precision and strategic depth in equal measure. Its Global Finals tournament and dedicated community have kept it a pillar of esports for over a decade.',
     '2024-03-01', 3, 1, 13, 1),

(14, 'Civilization VI',       'Turn-based 4X strategy spanning human history',
     'Civilization VI challenges players to build an empire to stand the test of time across multiple millennia. With district-based city building, 50+ leaders with unique abilities, and multiple victory conditions including science, culture, religion, and domination, each game tells a different story. The Rise and Fall and Gathering Storm expansions added loyalty mechanics and climate systems.',
     '2024-03-05', 3, 2, 14, 1),

(15, 'Age of Empires IV',     'Classic RTS franchise reborn with historical campaigns',
     'Age of Empires IV revives the beloved real-time strategy series with eight distinct civilizations drawn from medieval history, each with unique units and architectural styles. Documentary-style in-game footage provides real-world context for battles, while the competitive multiplayer mode satisfies veterans with deep economic and military optimization challenges.',
     '2024-03-08', 3, 3, 15, 1),

(16, 'XCOM 2',                'Turn-based tactics with permadeath alien resistance',
     'XCOM 2 puts players in command of a global resistance force against an alien occupation, using turn-based tactical combat where every decision carries the weight of permanent consequences. Procedurally generated maps, randomized soldier names and appearances, and an unforgiving difficulty curve have made it one of the most celebrated tactical strategy games of its generation.',
     '2024-03-12', 3, 1, 16, 1),

(17, 'Total War: Warhammer III', 'Grand strategy meets real-time battles in a fantasy world',
     'Total War: Warhammer III combines the franchise\'s grand strategic campaign map with spectacular real-time battles featuring fantasy races including Chaos Daemons, Cathay, and Ogre Kingdoms. The Immortal Empires combined campaign merges all three Warhammer titles into a single massive map, offering hundreds of hours of strategic conquest across the Old World and beyond.',
     '2024-03-16', 3, 2, 17, 1),

(18, 'Teamfight Tactics',     'Auto-chess strategy with League of Legends champions',
     'Teamfight Tactics is Riot Games\' auto-battler set in the League of Legends universe, where eight players build and position teams of champions to battle each other automatically. The strategic depth lies in synergy combinations, economy management, and item augmentation. Frequent set rotations keep the meta fresh and introduce new mechanics and themed champion rosters every few months.',
     '2024-03-20', 3, 3, 18, 1),

(19, 'The Legend of Zelda: TotK', 'Epic open-world adventure with infinite creativity',
     'Tears of the Kingdom expands on Breath of the Wild\'s foundation with the sky islands of Hyrule and a vast underground realm, complemented by the revolutionary Ultrahand and Fuse abilities. Players can build machines, weapons, and vehicles from environmental objects, creating one of the most creative open-world sandboxes ever crafted. A masterpiece of emergent gameplay design.',
     '2024-04-01', 4, 1, 19, 1),

(20, 'Elden Ring',            'FromSoftware\'s open-world masterpiece with George R.R. Martin',
     'Elden Ring brought FromSoftware\'s demanding action RPG design into a breathtaking open world crafted in collaboration with George R.R. Martin. The Lands Between is filled with legacy dungeons, hidden questlines, and over 100 unique boss encounters. Its non-linear structure rewards exploration and curiosity, while the uncompromising difficulty creates one of gaming\'s most satisfying challenges.',
     '2024-04-05', 4, 2, 20, 1),

(21, 'Red Dead Redemption 2', 'Rockstar\'s cinematic open-world Western epic',
     'Red Dead Redemption 2 is a sprawling open-world epic set in the twilight of the American frontier. Arthur Morgan\'s story is one of gaming\'s most emotionally resonant narratives, told through a living world filled with wildlife, dynamic weather, and NPCs that remember player interactions. Rockstar\'s attention to detail across over 60 hours of story content remains unmatched.',
     '2024-04-10', 4, 3, 21, 1),

(22, 'Genshin Impact',        'Free-to-play open-world RPG with gacha mechanics',
     'Genshin Impact launched to worldwide acclaim with its stunning anime-inspired open world of Teyvat and element-based combat system. miHoYo\'s live-service model delivers new regions, characters, and story quests every six weeks. With a cast of over 70 playable characters across seven elemental affinities, team-building for Spiral Abyss clears has created a deep theorycrafting community.',
     '2024-04-15', 4, 1, 22, 1),

(23, 'Hogwarts Legacy',       'Open-world wizarding RPG set in the 1800s',
     'Hogwarts Legacy fulfilled a dream for Harry Potter fans with a fully realized open-world set in the 1800s wizarding world. Players customize a fifth-year student, explore Hogwarts castle and surrounding regions, master spells from the series, and uncover an ancient secret tied to ancient magic. Its detailed recreation of iconic locations set a new bar for licensed game worlds.',
     '2024-04-20', 4, 2, 23, 1),

(24, 'Baldur\'s Gate 3',      'GOTY 2023 — definitive D&D RPG with endless choices',
     'Baldur\'s Gate 3 from Larian Studios took the Game of the Year crown with its unprecedented depth of player agency, co-op roleplay, and faithful Dungeons & Dragons 5th Edition mechanics. With twelve playable classes, over 170 hours of branching content, and a world that reacts to nearly every decision made, it stands as the definitive achievement in narrative RPG design.',
     '2024-04-25', 4, 3, 24, 1);