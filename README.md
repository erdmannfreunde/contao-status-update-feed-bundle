# Contao Status-Update-Feed Bundle

Stellt Status-Updates als Feed bereit, den andere Contao-Installationen mit dem [contao-status-update-bundle](https://github.com/erdmannfreunde/contao-status-update-bundle) abonnieren können. So lassen sich Ankündigungen – etwa zu kritischen Sicherheitsupdates – einmal zentral pflegen und erscheinen automatisch im Backend-Dashboard aller abonnierenden Installationen.

Das Bundle wird nur auf der Installation benötigt, die den Feed bereitstellt. Abonnierende Installationen brauchen nur das kostenlose Status-Updates Bundle.

Die Erweiterung ist **kostenpflichtig** und benötigt einen Lizenzschlüssel (siehe Abschnitt „Lizenzschlüssel“). Mehr Infos und Bestellung: [erdmann-freunde.de/leistungen/contao-erweiterungen/status-update](https://erdmann-freunde.de/leistungen/contao-erweiterungen/status-update#status-update-feed)

## Installation

```bash
composer require erdmannfreunde/contao-status-update-feed-bundle
```

Anschließend `contao:migrate` ausführen und den Lizenzschlüssel eintragen (siehe Abschnitt „Lizenzschlüssel“).

## Einrichtung

1. In der Seitenstruktur eine Seite vom Typ **Status-Update-Feed** anlegen, z.B. mit dem Titel „Standard“ und dem Alias `status-updates`. Der Feed ist dann unter `/status-updates.json` erreichbar.
2. Bei einem Status-Update unter **In Abo-Feeds veröffentlichen** (Legende „Veröffentlichung“) einen oder mehrere Feeds auswählen. Das Update muss außerdem veröffentlicht sein.
3. Auf den abonnierenden Installationen die Feed-URL in der `config/config.yaml` eintragen (siehe README des Status-Updates Bundles):

```yaml
erdmannfreunde_contao_status_update:
    feed_url: 'https://www.example.com/status-updates.json'
```

Die Abonnenten holen den Feed stündlich ab.

## Mehrere Feeds

Jede Seite vom Typ **Status-Update-Feed** ist ein eigener Feed mit eigener URL. Der Seitentitel ist der Name, unter dem der Feed in der Auswahl beim Status-Update erscheint; er wird nicht ausgegeben und darf daher auch intern sein (z.B. Kundennamen). Der Alias landet in der URL – hier besser neutral bleiben.

Beispiel: Fast alle Kund:innen bekommen „zwischen 9 und 15 Uhr“, zwei Kund:innen brauchen ein eigenes Zeitfenster.

| Seitentitel | Alias / URL | Abonniert von |
| --- | --- | --- |
| Standard | `/status-updates.json` | fast allen Kund:innen |
| Zeitfenster A | `/zeitfenster-a.json` | Kunde A |
| Zeitfenster B | `/zeitfenster-b.json` | Kunde B |

- Gleicher Text für alle: ein Status-Update, alle drei Feeds auswählen.
- Abweichende Uhrzeit: ein Status-Update „zwischen 9 und 15 Uhr“ nur für „Standard“, dazu je eine Kopie mit der passenden Uhrzeit nur für „Zeitfenster A“ bzw. „Zeitfenster B“. Beim Kopieren wird die Feed-Auswahl bewusst nicht übernommen, damit eine Variante nicht versehentlich im falschen Feed landet.

In der Übersicht der Status-Updates steht hinter jedem Eintrag, in welchen Feeds er veröffentlicht ist, z.B. `[Feed: Standard, Zeitfenster A]`. So lassen sich Original und Varianten mit gleichem Titel auseinanderhalten.

Jede Installation abonniert genau einen Feed. Abschluss und Abschluss-Mail laufen pro Status-Update – die Varianten werden also einzeln abgeschlossen.

## Was im Feed landet

- Alle veröffentlichten Status-Updates, die diesem Feed zugeordnet sind und deren Datum nicht länger als 30 Tage zurückliegt (30 Tage = längster Nachlauf).
- Übertragen werden Titel, Beschreibung, Datum, Vorlauf, Nachlauf und der Abgeschlossen-Status. Der Anzeigebereich und die Frontend-Felder bleiben lokal.
- Insert-Tags in der Beschreibung werden vor der Ausgabe ersetzt, relative Links in absolute umgewandelt.

Änderungen am Titel, Text oder Datum übernehmen die Abonnenten beim nächsten Abruf. Wird ein Feed abgewählt oder das Update deaktiviert, verschwindet es bei den Abonnenten dieses Feeds bzw. bei allen.

Wird ein Update als **abgeschlossen** markiert, schließen es auch alle Abonnenten ab und verschicken ihre Abschluss-Mail (sofern dort aktiviert) – jeweils genau einmal. Das Zurücknehmen des Abschlusses wird nicht übertragen.

## Feed-Format (Version 1)

```json
{
  "version": 1,
  "items": [
    {
      "id": 12,
      "updated": 1761818400,
      "title": "Kritisches Sicherheitsupdate für Contao",
      "description": "<p>Wir spielen das Update am 04.11. zwischen 9 und 11 Uhr ein.</p>",
      "date": "2026-11-04",
      "show_days_before": "7_days",
      "show_days_after": "1_day",
      "completed": false
    }
  ]
}
```

- `id` ist die Datensatz-ID und bleibt stabil, auch wenn der Termin verschoben wird.
- `updated` ist der Änderungszeitpunkt; Abonnenten aktualisieren nur, wenn er sich ändert.
- `date` wird ohne Uhrzeit übertragen und beim Abonnenten als lokale Mitternacht interpretiert.
- `completed` ist optional; fehlt das Feld, gilt das Update als offen.

Die Antwort trägt den Header `X-Robots-Tag: noindex`. Caching lässt sich wie bei anderen Seiten über die Cache-Einstellungen der Feed-Seite steuern.

## Lizenzschlüssel

Für den Betrieb ist ein Lizenzschlüssel für das Produkt **Contao Status Update Feed** nötig. Bestellen lässt er sich auf der [Produktseite](https://erdmann-freunde.de/leistungen/contao-erweiterungen/status-update#status-update-feed). Die Lizenzen verwaltet das [contao-license-bundle](https://github.com/erdmannfreunde/contao-license-bundle), das automatisch mitinstalliert wird.

1. In der Übersicht der Status-Updates über das Menü „···“ den Eintrag **Lizenz** öffnen.
2. Einen neuen Eintrag für „Contao Status Update Feed“ anlegen und den Schlüssel eintragen. Der Status wird beim Speichern direkt gegen den Lizenzserver geprüft.

Ohne gültige Lizenz:

- antworten alle Feed-Seiten mit HTTP 403 statt mit dem Feed,
- behalten abonnierende Installationen ihren letzten Stand (es wird nichts gelöscht) und protokollieren den Fehler im System-Log,
- zeigt die Übersicht der Status-Updates einen entsprechenden Hinweis.

Ist der Lizenzserver nicht erreichbar, gilt eine Karenzzeit von 7 Tagen. Prüfen lässt sich der Status auch per CLI: `vendor/bin/contao-console erdmannfreunde:license:check`.

## Redaktionsregeln

Der Feed ist öffentlich abrufbar. Texte deshalb so schreiben, als wären sie öffentlich:

- keine Kunden- oder Servernamen
- keine technischen Details zu einer Sicherheitslücke, bevor Contao sie selbst veröffentlicht hat

Die Inhalte landen in allen abonnierenden Backends. Die bereitstellende Installation sollte deshalb gut geschützt sein (z.B. Zwei-Faktor-Authentifizierung für alle, die Status-Updates bearbeiten dürfen).

## Anforderungen

- PHP 8.2 oder höher
- Contao 5.3 oder höher
- erdmannfreunde/contao-status-update-bundle 1.5 oder höher
- erdmannfreunde/contao-license-bundle 1.1 oder höher (wird automatisch installiert)
- ein gültiger Lizenzschlüssel

## Lizenz

Dieses Bundle ist unter LGPL-3.0-or-later lizenziert.
