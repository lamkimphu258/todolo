-- Doctrine Migration File Generated on 2021-08-29 12:59:21

-- Version DoctrineMigrations\Version20210829125755
ALTER TABLE "user" ADD subscribe_to_newsletter BOOLEAN DEFAULT 'false' NOT NULL;
