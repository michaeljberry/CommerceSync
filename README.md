# CommerceSync
A system to manage online orders across Amazon, Ebay, Walmart, Reverb, and Big Commerce stores

All channel orders (excepting Amazon FBA), are brought into the DB and turned into XML files for insertion into an AS-400 IBM server based ERP. 

After insertion into the ERP, updated product inventory quantities are extracted and sent to the separate channels. Currently inventory updates are sent to EcomDash as that is the current SaaS being used by the client, though originally, it was pushed out directly to each channel. Using a procedural approach to update inventory quantities became too unwieldy, so the current approach was decided on for a short-term fix. Now that OOP is being used, inventory updates will eventually be pushed directly to each channel and avoid the middleman.

Once orders have shipped within the ERP, tracking information (number and carrier) are retrieved and pushed directly to each channel.

TODO::

(Ongoing - Write tests for current code)
1. OOP approach to directly updating each channel with inventory updates.
2. Forms to manage API keys/Oauth.
3. Forms to create/edit users.
4. Build more robust channel inventory updater to allow for products disallowed from being sold on certain channels.
5. Build background taxonomy mapper to allow for much more robust handling of data between channels.
6. Create products and list directly to each channel
7. Integrate the 'V' in MVC after 'M' and 'C' are completed. 
8. And probably a whole bunch more stuff...
