# Companion-managed configuration

This project's tooling configuration is generated and kept in sync by
**Companion**, netzmacht's PHP project configuration toolkit. The source of
truth is `companion.json` in the project root together with the receipts it
lists (presets shipped with Companion). The files below are written by
`project:configure`; hand edits to Companion-owned content are overwritten on
the next run.

## Rules

- Never edit the *exclusive* files below. They are regenerated entirely and
  `Edit` deny rules in `.claude/settings.json` enforce this. If an edit is
  rejected, this is why.
- In *shared* files, only touch the parts Companion does not manage (see the
  table). Never edit the Companion-managed sections by hand.
- To change generated content, change its source instead: the matching
  `tools.<name>` block in `companion.json` (or the receipt it comes from) and
  then run Companion's `project:configure` command against this project.
  Running that command and committing its result is the intended workflow.
- If the Companion CLI is not available in your environment, describe the
  required `companion.json` change to the user and ask them to run
  `project:configure`.

## Managed resources

| Path | Ownership | What Companion manages |
| --- | --- | --- |
| `.claude/rules/companion.md` | exclusive | Generated entirely by Companion (this file). |
| `.claude/rules/phpcq.md` | exclusive | Rule document contributed by Companion in full; hand edits are overwritten on the next run. |
| `.claude/settings.json` | shared | Companion owns `permissions.allow`, `permissions.deny`, `permissions.ask`, `env`, `skillOverrides` and the `hooks` events it configures; existing entries are merged with the configured ones rather than replaced, other keys and other hook events are preserved. An entry can be forced out of `permissions.*` by setting it to `false` in `companion.json`'s map form. |
| `.editorconfig` | exclusive | Generated entirely from the Companion configuration. |
| `.gitattributes` | shared | Companion owns the `# <Group>` sections it writes and merges into existing groups; lines outside those groups (`# Custom`) are preserved. |
| `.gitignore` | exclusive | Generated entirely from the Companion configuration; hand-added entries are lost on the next run. Add project-specific ignores to the `gitignore` block of `companion.json` instead. |
| `.phpcq.yaml.dist` | exclusive | Generated entirely from the Companion configuration. |
| `composer.json` | shared | Companion applies receipt defaults, rewrites version constraints of matching packages and sets the `php` requirement; everything else is preserved. Prefer `composer require` for dependencies. |
| `psalm.xml` | shared | Companion sets root attributes and adds `projectFiles` / `ignoreFiles` entries; existing entries, `issueHandlers`, `plugins` and everything else are preserved. |
