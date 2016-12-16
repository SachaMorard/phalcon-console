# Migration


Migrations commands generate scripts with datetime versioning. 
In your configuration file, you must to define a config->application->migrationDir value. 
Then you have to manage your model metadatas with [sachoo/phalcon-model-annotations](https://github.com/sachoo/phalcon-model-annotations)

To know more about the power of Migrations, execute:
```bash
 ./console mig
```

This command should display something similar to:

```sh
Help:
  Migration Commands

Usage: Generate a Migration
  mig gen

Usage: Run all available Migrations
  mig run

Usage: Run just one migration up
  mig up

Usage: Run just one migration down
  mig down

Usage: Generate migration file with Diff beetween Models and your Databases
  mig diff

Usage: Show migration status
  mig status
```