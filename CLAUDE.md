# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

- **Viewing the site**: Open `index.html`, `tech.html`, or `about.html` in a browser directly, or run a local static server (e.g., `python -m http.server 8000`) from the repository root.
- **Testing PHP examples**: Navigate to `phptest/` and run `php -S localhost:8000` to test the form submissions (hello.php → action.php, random_names.php).
- **Linting**: No formal linting setup; HTML/CSS/JS can be checked manually or with online validators.
- **Running tests**: No automated test suite exists. Verify changes by opening the relevant pages in a browser and interacting with UI/components.

## Project Structure

- **Root**: Contains static site assets:
  - `index.html` – Home page (Field Notes journal)
  - `tech.html` – The Nexus experimental interface
  - `about.html` – About page (simplified journal view)
  - `styles.css` – Shared styles for index.html and about.html
  - `tech.css` – Styles specific to tech.html
  - `script.js` – Shared JavaScript (mobile menu toggle, form handling, year update)
- **phptest/**: Simple PHP demonstrations:
  - `hello.php` – Form collecting name and age
  - `action.php` – Displays submitted data
  - `random_names.php` – More complex form with shuffling and filtering logic
- **.claude/**: Claude Code configuration:
  - `settings.json` – Permission and behavior preferences
  - `settings.local.json` – Local overrides
  - `agents/security-audit-on-change.md` – Agent definition for security checks on file changes

## Architecture Overview

The site is a small, static HTML/CSS/JavaScript presentation with two main sections:
1. **Field Notes (index.html)** – A journal-like layout with hero section, featured cards, notes grid, and newsletter signup.
2. **The Nexus (tech.html)** – Similar layout but with a digital/aesthetic theme, featuring Nexus logs and transmissions.
Both pages share `script.js` for interactive behavior (mobile navigation, form validation, dynamic year). Styling is split between a shared stylesheet (`styles.css`) and a page-specific one (`tech.css`).

The PHP files in `phptest/` are isolated examples for learning form handling and are not integrated into the main site.

## Coding Standards

### Commenting Practices

1. **Comments are not a substitute for clear and expressive coding and naming practices**
   - Poor Example:
     ```
     // Add taxes to the invoice amount
     x = x + y;
     ```
   - Good Example:
     ```
     InvoiceAmount = InvoiceAmount + TaxAmount;
             OR
     InvoiceAmount += TaxAmount;
     ```
   - Note: The best practice here is more expressive coding, and the comment is no longer even needed. Object names should clearly say their business meaning, without abbreviation or ambiguity. “If the code is so complicated that it needs to be explained it’s nearly always better to improve the code than it is to add comments. Make the code itself clearer, then use summary or intent comments”. Comments do not turn bad code into good code.
   - Code p. 786. See Clean p. 55,

2. **Comments which merely restate the obvious and clear outcome of the code are redundant and should not be used.**
   - Poor Example:
     ```
     // Add taxes to the invoice amount
     InvoiceAmount += TaxAmount;
     ```
   - Good Example:
     ```
     InvoiceAmount += TaxAmount;
     ```
   - Note: After coding using the technique of pseudo coding you may have superfluous comments which should be removed.
   - Code p. 786 Clean p. 60

3. **Comments need to be updated and refactored (removing obsolete comments), just like code.**
   - If you make changes to the code, check the nearby comments to see if anything needs to be updated.
   - Code p. 806. Clean p. 286

4. **Comments should include the developers initials and date (MM/DD/YY)**
   - Poor Example:
     ```
     // New business policy requires a discount code
     ```
   - Good Example:
     ```
     // RKG 11/01/20 New business policy requires a discount code
     ```
   - Note: This is so we can quickly see who made the comment, and the age of it, without having the extra time-consuming step of digging it out of source control. The date helps to quickly identify obsolete code and comments to be removed. If you want to ask the previous developer a question it is so much easier to have the name right there in the code than to try to get it from source control.

5. **A good use of comments is to describe intent – the “why”**
   - Poor Example:
     ```
     If( DiscountCode!=””)
     {
         Do this code …
     }
     ```
   - Good Example:
     ```
     // RKG 11/01/20 New business policy requires a discount code for ALL purchases
     If( DiscountCode!=””)
     {
         Do this code …
     }
     ```
   - Note: The code should describe “what” is being done, but it can never describe “why” it is being done. This can only be added through comments and other documentation. If a later developer does not understand the reason for the code, it might be altered or removed in a way that creates a defect.
   - Code p. 787 Clean p. 56

   - Poor Example:
     ```
     If( leftstr( UserEntry,1,1)=”&”)
     {
         Do this code …
     }
     ```
   - Good Example:
     ```
     // RKG 11/01/20 & is used to tag a replacement value from the database
     If( leftstr( UserEntry,1,1)=”&”)
     {
         Do this code …
     }
     ```
   - Note: Without this comment it impossible to know why we are looking for a “&” in the subject string.

6. **Summary type comments can reduce code-reading time by condensing the function of many lines of code into one comment. Use at your discretion if the logic is complicated, or in the alternative extract the code into a function with a self-documenting name.**
   - Poor Example:
     ```
     Current=1;
     Previous = 0;
     Sum =1;
     Num=15;
     For (int i=0; i<  num; i++)
     {
         System.out.println( “Sum = “ + Sum);
         Sum = Current + Previous;
         Current = Sum;
     }
     ```
   - Good Example:
     ```
     // RKG 11/01/20 Write out the Fibonacci sequence until reaching Num digits
     Current=1;
     Previous = 0;
     Sum =1;
     Num=15;
     For (int i=0; i<  num; i++)
     {
         System.out.println( “Sum = “ + Sum);
         Sum = Current + Previous;
         Current = Sum;
     }
     ```
   - Note: It would take quite a while to think through what this code sequence is doing even though the steps are clear. The summary comment saves a lot of time in reading the code.
   - Code p. 787
   - Another way to document this is to move a complicated code sequence to a function. If this code is used more than one place, then moving to a function is a good solution. But creating a function instead of a simple one line comment is not efficient either.
   - Good Example:
     ```
     Print get_fibonachi_squence(1);
     ```
   - Public string get_fibonachi_squence (int ending_number){
         String return_value;
         Current=1;
         Previous = 0;
         Sum =1;
         For (int i=0; i<  ending_number; i++)
         {
             return_value +=  “Sum = “ + Sum;
             Sum = Current + Previous;
             Current = Sum;
         }
     }

7. **Routines (methods) and classes should usually have a few lines of summary comment to save time in reading the code, such as explanation of the return values, and any needed explanation about the process.**
   - Poor Example:
     ```
     // RKG 11/24/20 Lookup the invoice amount
     Public real lookup_invoice_amount( int invoice_number)
     {
          Code …
     }
     ```
   - Note: This comment does not give any more information than the declaration
   - Good Example:
     ```
     // RKG 11/24/20 check the invoice status, and return the amount if active.
     // return -1 if the invoice number is not found  or inactive.  Returns 0 if the invoice is cancelled.
     Public real lookup_invoice_amount( int invoice_number)
     {
          Code …
     }
     ```
   - Note: This summary comment gives a quick overview of the function and provides very useful information that the programmer would otherwise have to spend a lot of time to read, and might miss a crucial detail. The comments should also note any side effect of the method or any global variables that are altered.
   - Code p. 806

8. **Other useful comments can include legal notices, TODO reminders, warnings to other developers, and other information that cannot be expressed in the code itself.**
   - Code p. 786 – 788 Clean p. 55-58

9. **Write the comments first, such as by pseudo coding.**
   - This will help you to organize your thoughts and make the logic mentally clear. Then remove any redundant or superfluous comments.
   - Code p. 791
   - Note: Comments written before coding can also have the benefit of helping the developer organize his thoughts and logic from a higher level. This reduces design errors and “painting yourself into a corner” then having to redo the code. This is a chief benefit of pseudo coding in which you first outline your code in general steps, then pseudo code the detailed logic, and then finally write the actual programming syntax.

10. **When removing or changing code, comment out the old code with a comment on the reason why. Remove any commented-out code that is obsolete, which is generally older than 6 months.**
    - Poor Example:
      ```
      Public real lookup_invoice_amount( int invoice_number)
      {
           Code …
      }
      ```
    - Note: Suppose the programmer is looking for a new defect in this method. It is not easy to see recent changes without looking through source control, possibly many commits.
    - Good Example:
      ```
      Public real create_invoice( int customer_number, real amount)
      {
           // RKG 11/24/20 there was a bug that was creating duplicate invoices
          // when the customer_number was not yet created.  I removed this logic
          // and added logic to return 0 in that case
      // Code …
          Code …
      }
      ```
    - Note: Source control has a record of removed code but can never say WHY the developer removed it or what his thinking was for that. As developers we spend a lot of our time fixing defects that were recently introduced. Having information about recent changes in the plain display of the code can save a lot of time, especially when we know when the problem first appeared and the comments have a date for the changes. To avoid growing clutter, just remove any old code and comments that are no longer useful. Six months is just a rule of thumb and the developer can remove such old code sooner if the code is getting convoluted.

11. **As a minimum of 10% of line of code should have or be comments, but more commonly 25% of complex logic should.**

## Naming Standards

### Purpose
A well named data model is easy to share with anybody, in addition, you are much more likely to avoid errors or issues in the data model if you follow a naming standard.

### General Rules
- **Variables, tables, and fields always use singular words and never plural.** This saves a lot of defects because you don’t have to remember if you should add an “s” to this name or that one. Never add the “s”! Use “invoice”, “customer”, or “item” NOT “invoices”, “customers” or “items”.
- **If a business asset contains multiple words, separate with underscores for easy reading.** For instance, “epay branding logo becomes” becomes “epay_branding_logo”. Do not use spaces or other separators between words.
- **Avoid names longer than 30 characters for a table name, or over 20 characters for a field name.** For a long name, it may be OK to condense it by omitting needless words. In some cases, longer names could be more useful and may merit an exception. The primary consideration is that the meaning of the name should be easily understood by a diverse group of readers. Saving time in reading and understanding is much more important than saving time in writing the code.
- **Avoid names that are too short to be distinct.** For short names, pick names that are clearly understandable to many readers.

### JavaScript Code Naming Conventions
- When it comes to Javascript development, we have two variants: a “.vue” file and a “.js” file.
- **Avoid default exports from modules.** This is purely for legacy and should be avoided. For Javascript naming/coding standards, one of the examples that can be referred to is: https://github.com/airbnb/javascript
- **Vue specific:**
  - Use component name in template. Vue supports both `<MultiCatalog ... />` and `<multi-catalog ... />` — Use the latter
  - Same for prop names
  - No complicated login in template expressions. Refactor to a `computed` property or `methods`

### Database Tables and Fields
- Use a fully descriptive field names that don’t duplicate, as this can cause problems in joins and ambiguity in code. For example don’t use “phone” in both the customer and vendor table use “vendor_phone” and “customer_phone”.
- Do not include any kind of generic prefix for all fields of a table. Example “payer_name” not “grl_payer_name”.
- The first name in a Table needs to describe the functional area it belongs to, or the purpose of the table, for example, tables used for Collections functionality should start with “collections” and the type of table follows such as “collections_payment”, “collections_status” and so forth. This helps to group related tables together in the schema. If the table is a generic table, then use a descriptive generic name.

### Abbreviations
- Avoid abbreviations, except those that are well known and generally accepted. Words under 8 letters should never be abbreviated.
- Acceptable abbreviations include:
  - trx    Transaction
  - std    Standard

### Acronyms
- For acronyms use all caps, just as you would when writing a document. For example, if you have to use an acronym in a variable or field name like “user_FTP” not “user_ftp”.
- Only use acronyms that are widely understood such as these:
  - FTP    File Transfer Protocol
  - SSH    Secure SHell
  - SMS    Short Message Service (text message)
  - NCOA   NationalChangeOfAddress
  - DNC    Do not call list
  - ACH    Automated Clearing House (electronic check)
- Acronyms should never start a table name.
- File names should begin with the name of the functional area, as described above.
- “ID” is an acronym for “Identification”, so it is always capitalized as in “payer_ID”. For primary keys “serial” is preferred to ID, such as “payer_serial” to avoid duplication with other types of “ID”.

## Typical Workflow

- Edit HTML/CSS/JS files and refresh the browser to see changes.
- For PHP modifications, start the built-in server in `phptest/` and submit forms to test.
- No build step or dependencies; changes take effect immediately.

## Guidelines

- Reuse existing styles and components when adding new UI elements (e.g., variant of `feature-card` or `note`).
- Keep JavaScript minimal; prefer CSS for interactions where possible.
- When adding new pages, follow the existing header/footer structure and link appropriately.
- The PHP sandbox is for experimentation only; do not rely on it for production logic.